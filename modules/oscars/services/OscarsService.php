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
            CURLOPT_USERAGENT => 'Meican',
            CURLOPT_URL => $oscarsUrl
        );
        curl_setopt_array($ch , $options);
        $output = curl_exec($ch);
        curl_close($ch);

        Yii::trace(json_decode($output));
        OscarsService::saveCircuits(json_decode($output));
        return true;
    }

    private static function saveCircuits($circuits) {
        $conns = [];

        foreach ($circuits as $circuit) {
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
