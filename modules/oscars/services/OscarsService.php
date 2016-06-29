<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\oscars\services;

use Yii;

use meican\base\utils\DateUtils;
use meican\base\utils\StringUtils;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionPath;

/**
 * @author Maurício Quatrin Guerreiro
 */
class OscarsService {

    /**
     * Contact OSCARS instance, get active and scheduled circuits
     * and save in MEICAN database. After that, updates all circuits
     * of same type for consistence.
     */
    public static function loadCircuits($oscarsUrl) {
        $ch = curl_init();
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_USERAGENT => 'Meican',
            CURLOPT_URL => $oscarsUrl,
        );
        curl_setopt_array($ch , $options);
        $output = curl_exec($ch);
        curl_close($ch);

        Yii::trace($output);
        
        if($output != null) {
            OscarsService::saveCircuits(json_decode($output));
            return true;
        } else {
            return false;
        }
    }

    private static function saveCircuits($circuits) {
        $conns = [];
        $activeCircuitsGRI = [];

        foreach ($circuits as $circuit) {
            if($circuit->status == 'ACTIVE')
                $activeCircuitsGRI[] = $circuit->gri;

            OscarsService::saveCircuit(
                $circuit->gri, 
                $circuit->description, 
                $circuit->status, 
                $circuit->startTime, 
                $circuit->endTime, 
                $circuit->bandwidth, 
                $circuit->path
            );
        }

        $toFixCircuits = Connection::find()
            ->where(['dataplane_status'=> 'ACTIVE', 'type'=> 'OSCARS'])
            ->andWhere(['not in', 'external_id', $activeCircuitsGRI])
            ->all();

        foreach ($toFixCircuits as $conn) {
            $conn->dataplane_status = 'INACTIVE';
            $conn->save();
        }
    }

    private static function saveCircuit($gri, $desc, $status, $start, $end, $bandwidth, $path) {
        $conn = Connection::findOne(['external_id'=>$gri]);
        if($conn == null) {
            $conn = new Connection;
            $conn->external_id = $gri;
            $conn->name = $desc;
            $conn->type = 'OSCARS';
            $conn->status = 'PROVISIONED';
            $conn->version = 1;
            $conn->dataplane_status = $status == 'ACTIVE' ? 'ACTIVE' : 'INACTIVE';
            $conn->auth_status = 'UNEXECUTED';
            $conn->resources_status = 'PROVISIONED';
            $conn->start = DateUtils::timestampToDB($start);
            $conn->finish = DateUtils::timestampToDB($end);
            $conn->bandwidth = $bandwidth;

            if($conn->save()) {
                Yii::trace($path);
                for ($i=0; $i < count($path); $i++) { 
                    $point = new ConnectionPath;
                    $point->conn_id = $conn->id;
                    $point->path_order = $i;
                    $urnArray = explode(":", $path[$i]);
                    Yii::trace($urnArray);
                    $point->vlan = explode('=', $urnArray[7])[1];
                    Yii::trace($point->vlan);
                    $point->domain = explode('=', $urnArray[3])[1];
                    Yii::trace($point->domain);
                    $urnArray[7] = '';
                    $urnArray[6] = '';
                    $point->port_urn = substr(implode(':', $urnArray), 0, -2); 
                    Yii::trace($point->port_urn);
                    $point->save();
                }
                OscarsService::associateNSICircuits($conn);
            } else Yii::trace($conn->getErrors());
        } else {
            $conn->dataplane_status = $status == 'ACTIVE' ? 'ACTIVE' : 'INACTIVE';
            $conn->save();
        }
    }

    //funcao temporaria para associar circuitos NSI.
    //Claramente deve se pensar futuramemnte em como fazer isso de uma forma
    //mais correta. Portas NSI e NMWG nao sao traduziveis entre si. O que faco aqui
    //é puramente um conhecimento da topologia atual da RNP, nao funcionaria
    //em nenhum outro dominio e inclusive pode parar de funcionar a qualquer momento
    //pois é uma regra estatica e hardcoded observada em redes que usam o OSCARS como
    //elemento de baixo nivel.
    private static function associateNSICircuits($conn) {
        $srcPoint = $conn->getFirstPath()->asArray()->one();
        $dstPoint = $conn->getLastPath()->asArray()->one();

        //procura circuitos NSI ativos e agendados para o mesmo horario
        //que o circuito OSCARS.
        $nsiActiveCircuits = Connection::find()
            ->where([
                'type'=> 'NSI',
                'start'=> $conn->start,
                'finish' => $conn->finish])
            ->all();

        foreach ($nsiActiveCircuits as $nsiCircuit) {
            $candidateSrcPoint = $nsiCircuit->getFirstPath()->asArray()->one();
            $candidateDstPoint = $nsiCircuit->getLastPath()->asArray()->one();
            Yii::trace($candidateSrcPoint);
            Yii::trace($candidateDstPoint);

            //Nao podemos atualmente garantir que a traducao de URNs funcione para outros dominios
            if($candidateSrcPoint['domain'] != 'cipo.rnp.br' ||
                $candidateDstPoint['domain'] != 'cipo.rnp.br') {
                return;
            }

            //URNs NMWG sao padronizadas entao podemos parsear
            //URNs NSI nao sao, logo teremos que fazer uma busca na URN
            //pelos elementos node e port da URN NMWG. Se estiverem presentes,
            //iremos considerar que representam a mesma porta.
            $srcDev = explode(':', $srcPoint['port_urn']);
            $srcDev = explode('=', $srcDev[4])[1];
            $dstDev = explode(':', $dstPoint['port_urn']);
            $dstDev = explode('=', $dstDev[4])[1];
            $srcPort = explode(':', $srcPoint['port_urn']);
            $srcPort = explode('=', $srcPort[5])[1];
            $dstPort = explode(':', $dstPoint['port_urn']);
            $dstPort = explode('=', $dstPort[5])[1];
            $dstPort = str_replace('/', '_', $dstPort);
            $srcPort = str_replace('/', '_', $srcPort);
            Yii::trace($srcDev);
            Yii::trace($dstDev);

            //Se ambos paths comecam e terminam na mesma VLAN e porta
            //eles representam o mesmo circuito.
            if($srcPoint['vlan'] == $candidateSrcPoint['vlan'] &&
                $dstPoint['vlan'] == $candidateDstPoint['vlan'] &&
                StringUtils::contains($srcDev, $candidateSrcPoint['port_urn']) &&
                StringUtils::contains($dstDev, $candidateDstPoint['port_urn']) &&
                StringUtils::contains($srcPort, $candidateSrcPoint['port_urn']) &&
                StringUtils::contains($dstPort, $candidateDstPoint['port_urn'])) {

                $conn->parent_id = $nsiCircuit->id;
                $conn->save();
            }
        }
    }
}
