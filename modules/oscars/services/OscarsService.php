<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\oscars\services;

use Yii;

use meican\base\components\DateUtils;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionPath;

/**
 * @author Maurício Quatrin Guerreiro
 */
class OscarsService {

    /**
     * Contact OSCARS instance, get (active and scheduled) circuits
     * and save in MEICAN database.
     */
    public static function loadCircuits($oscarsUrl) {
        //this service is based on console scripts.
        //to execute this service from web, the ConsoleService
        //is required to pass the request to a console app instance
        if (Yii::$app instanceof \yii\web\Application) {
            ConsoleService::run('oscars/service/load-circuits');
            Yii::trace('Loading circuits from OSCARS...');
            return true;
        } 

        $bridgePath = '/Users/mqg/Documents/workspacejee/oscars-bridge';
        $output = [];
        //exec('export JAVA_HOME=/usr; mvn test -f /Users/mqg/Documents/workspacejee/oscars-bridge', $output);
        Yii::trace($output);
        OscarsService::saveCircuits($output);
        return true;
    }

    private static function saveCircuits($output) {
        $output = [
            '[INFO] --- exec-maven-plugin:1.5.0:java (default) @ oscars-bridge ---',
            '=======START===RESERVATIONS=======',
            '=======START===CIRCUIT========',
            'GRI: cipo.rnp.br-9133',
            'Login: rnp-agg-user',
            'Description: SP2-SC MEICAN 1G',
            'Status: ACTIVE',
            'Start Time: 1463501340',
            'End Time: 1467304620',
            'Bandwidth: 900',
            'Path: urn:ogf:network:domain=cipo.rnp.br:node=MXSP2:port=xe-3/0/0:link=*:vlan=206;urn:ogf:network:domain=cipo.rnp.br:node=MXSP2:port=ae7:link=10.0.0.142:vlan=206;urn:ogf:network:domain=cipo.rnp.br:node=MXSP:port=ae7:link=10.0.0.141:vlan=206;urn:ogf:network:domain=cipo.rnp.br:node=MXSP:port=xe-4/1/1:link=10.0.0.77:vlan=206;urn:ogf:network:domain=cipo.rnp.br:node=MXSC:port=xe-3/1/1:link=10.0.0.78:vlan=206;urn:ogf:network:domain=cipo.rnp.br:node=MXSC:port=ge-2/3/4:link=*:vlan=206;',
            '=======END===CIRCUIT========',
            '=======START===CIRCUIT========',
            'GRI: cipo.rnp.br-9109',
            'Login: rnp-agg-user',
            'Description: SP2-RJ MEICAN 1G',
            'Status: ACTIVE',
            'Start Time: 1462545540',
            'End Time: 1467302760',
            'Bandwidth: 1000',
            'Path: urn:ogf:network:domain=cipo.rnp.br:node=MXSP2:port=xe-3/0/0:link=*:vlan=205;urn:ogf:network:domain=cipo.rnp.br:node=MXSP2:port=xe-2/3/0:link=10.0.0.137:vlan=205;urn:ogf:network:domain=cipo.rnp.br:node=MXRJ:port=xe-2/1/0:link=10.0.0.138:vlan=205;urn:ogf:network:domain=cipo.rnp.br:node=MXRJ:port=xe-3/0/0:link=*:vlan=1705;',
            '=======END===CIRCUIT========',
            '=======END===RESERVATIONS=======',
            '[INFO] ------------------------------------------------------------------------',
        ];
        $conns = [];

        for ($i=0; $i < count($output); $i++) { 
            if($output[$i] == '=======START===CIRCUIT========') 
                OscarsService::saveCircuit(
                    str_replace('GRI: ',            '', $output[$i+1]), 
                    str_replace('Description: ',    '', $output[$i+3]), 
                    str_replace('Status: ',         '', $output[$i+4]), 
                    str_replace('Start Time: ',     '', $output[$i+5]), 
                    str_replace('End Time: ',       '', $output[$i+6]), 
                    str_replace('Bandwidth: ',      '', $output[$i+7]), 
                    str_replace('Path: ',           '', $output[$i+8])
                );
        }
    }

    private static function saveCircuit($gri, $desc, $status, $start, $end, $bandwidth, $path) {
        $conn = Connection::findOne(['external_id'=>$gri]);
        if($conn == null) {
            $conn = new Connection;
            $conn->external_id = $gri;
            //$conn->desc = $desc;
            $conn->type = 'OSCARS';
            $conn->status = 'PROVISIONED';
            $conn->version = 1;
            $conn->dataplane_status = 'ACTIVE';
            $conn->auth_status = 'UNEXECUTED';
            $conn->start = DateUtils::timestampToDB($start);
            $conn->finish = DateUtils::timestampToDB($end);
            $conn->bandwidth = $bandwidth;
            $conn->reservation_id = 1;

            if($conn->save()) {
                $path = explode(";", $path);
                Yii::trace($path);
                for ($i=0; $i < count($path) - 1; $i++) { 
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
            }
        }
    }
}
