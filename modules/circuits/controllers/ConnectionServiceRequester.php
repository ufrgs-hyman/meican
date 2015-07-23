<?php

namespace app\modules\circuits\controllers;

use Yii;
use app\components\AggregatorSoapClient;
use app\models\Connection;
use app\models\ConnectionPath;
use app\models\ConnectionLog;

class ConnectionServiceRequester {

    private $provider;
    private $csProvider;

    function __construct($connection) {
        $this->provider = $connection->getReservation()->one()->getProvider()->one();
        $this->csProvider = $this->provider->getConnectionService()->one();
    }

    public function requestCreate($conn) {
        if ($this->provider->isDummy()) {
            sleep(4);
            $date = new \DateTime();
            $conn->external_id = $date->format('YmdHis');
            $conn->confirmCreate();
            $conn->confirmCreatePath();
            return;
        } 
        
        $reserve = $conn->getReservation()->one();
        
        $directionality = "Bidirectional";
        $symmetricPath = "true";
        $parameter = "PROTECTED";
        $serviceType = "http://services.ogf.org/nsi/2013/12/descriptions/EVTS.A-GOLE";
        $firstPath = $reserve->getFirstPath()->one();
        $lastPath = $reserve->getLastPath()->one();
        $sourceSTP = $firstPath->getFullPortUrn()."?vlan=".$firstPath->vlan;
        $destSTP = $lastPath->getFullPortUrn()."?vlan=".$lastPath->vlan;
        
        $date = new \DateTime($conn->start);
        $start = $date->format('Y-m-d\TH:i:s.000-00:00');
        
        $date = new \DateTime($conn->finish);
        $finish = $date->format('Y-m-d\TH:i:s.000-00:00');
        
        /** Creating the SOAP request **/
        $schedule = array(
                "startTime" => $start, //start_time
                "endTime" => $finish
        );
        
        $paths = $reserve->getPaths()->all();
        $pathSize = count($paths);
        $waypoints = new \ArrayObject();
        
            if($pathSize > 2) {
                for ($i = 1; $i < ($pathSize - 1); $i++) {
                    $stp = $paths[$i]->getFullPortUrn()."?vlan=".$paths[$i]->vlan;
                    $stp = new \SoapVar(['stp'=>$stp], SOAP_ENC_OBJECT, NULL, NULL, null, NULL);
                    $orderedSTP = new \SoapVar($stp, SOAP_ENC_OBJECT, NULL, NULL, 'orderedSTP', NULL);
                    $waypoints->append($orderedSTP);
                }
                
                $ero = new \SoapVar($waypoints, SOAP_ENC_OBJECT, NULL, NULL, "ero", NULL);
                    
                $p2ps = array(
                        "capacity" => $reserve->bandwidth, //Bandwidth
                        "directionality" => $directionality, //Auto
                        "symmetricPath" => $symmetricPath, //Auto
                        "sourceSTP" => $sourceSTP,  //src_urn
                        "destSTP" => $destSTP, //dest_urn
                        "parameter" => $parameter, //Auto
                        $ero
                );
            } else {
                $p2ps = array(
                        "capacity" => $reserve->bandwidth, //Bandwidth
                        "directionality" => $directionality, //Auto
                        "symmetricPath" => $symmetricPath, //Auto
                        "sourceSTP" => $sourceSTP,  //src_urn
                        "destSTP" => $destSTP, //dest_urn
                        "parameter" => $parameter, //Auto
                );
            }
        
            $schedule = new \SoapVar($schedule, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
            $p2ps = new \SoapVar($p2ps, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
        
            $criteria = array(
                    "schedule" => $schedule,
                    "serviceType" => $serviceType,
                    "p2ps" => $p2ps
            );
        
            $criteria = new \SoapVar($criteria, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
        
            /** Tirando os espaços do Global Reservation ID, pois não é permitido **/
            $globalReservationId = str_replace(" ", "", $reserve->name);
        
            $params = array(
                    "globalReservationId" => $globalReservationId, //Reservation name
                    "description" => $reserve->name,
                    "criteria" => $criteria
            );
        
            try{
                $client = new AggregatorSoapClient($this->provider->nsa, $this->csProvider->url);
            }catch(\SoapFault $error){
                Yii::trace($error);
                return false;
            }
            
            $client->setAggHeader();
            
            try{
                $client->reserve($params);
            }catch(\SoapFault $error){
                Yii::trace($error);
                return false;
            }
        
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $response = $client->__getLastResponse();
            Yii::trace($response);
            $dom->loadXML($response);
        
            $connectionId = $dom->getElementsByTagName("connectionId")->item(0);
            $connectionId = $connectionId->textContent;
        
            if(isset($connectionId)){
                $conn->external_id = $connectionId;
                $conn->confirmCreate();
                    
                /** Connectivity Log **/
                $log = "Sent\n".
                        "Connection Id: ".$connectionId."\n".
                        "Action: reserve\n".
                        "DateTime: ".date(DATE_RFC822)."\n\n";
        
                Yii::trace($log);
                    
            }
            else {
                $conn->failedCreate();
                Yii::trace("Failed");
                return FALSE;
            }
            return TRUE;
    }
    
    public function requestCancel($conn) {
        if ($this->provider->isDummy()) {
            $conn->confirmCancel();
            return;
        }
        
        $params = array(
                "connectionId" => $conn->external_id
        );
         
        $client = new AggregatorSoapClient($this->provider->nsa, $this->csProvider->url);
        $client->setAggHeader();
        
        try{
            $client->terminate($params);
            Yii::trace($client->__getLastResponse());
        }catch(SoapFault $error){
            return false;
        }
    }
    
    public function requestReadPath($conn) {
        if ($this->provider->isDummy()) {
            $resPaths = $conn->getReservation()->one()->getPaths()->all();
            $i = 0;
            foreach ($resPaths as $resPath) {
                $connPath = new ConnectionPath;
                $connPath->path_order = $i;
                $connPath->conn_id = $conn->id;
                $connPath->domain = explode(":",$resPath->port_urn)[0];
                $i++;
                $connPath->port_urn = $resPath->port_urn;
                $connPath->vlan = $resPath->vlan;
                
                $connPath->save();
            }
            $conn->confirmReadPath();
            return;
        }
        
        $params = array(
                "connectionId" => $conn->external_id
        );
        
        $client = new AggregatorSoapClient($this->provider->nsa, $this->csProvider->url);
        $client->setAggHeader();
        $client->querySummary($params);
        
            /** Connectivity Log **/
            $log = "Sent\n".
                    "Connection Id: ".$conn->external_id."\n".
                    "Action: querySummary\n".
                    "DateTime: ".date(DATE_RFC822)."\n\n";
                
            Yii::trace($log);
        
    }
    
    public function requestCommit($conn) {
        if ($this->provider->isDummy()) {
            $conn->confirmCommit();
            return;
        }
        
        $params = array(
                "connectionId" => $conn->external_id
        );
        
        $client = new AggregatorSoapClient($this->provider->nsa, $this->csProvider->url);
        $client->setAggHeader();
        $client->reserveCommit($params);
        
            /** Connectivity Log **/
            $log = "Sent\n".
                    "Connection Id: ".$conn->external_id."\n".
                    "Action: reserveCommit\n".
                    "DateTime: ".date(DATE_RFC822)."\n\n";
        
            Yii::trace($log);
    }
    
    public function requestProvision($conn) {
        if ($this->provider->isDummy()) {
            $conn->confirmProvision();
            return;
        }
        
        $params = array(
                "connectionId" => $conn->external_id
        );
         
        $client = new AggregatorSoapClient($this->provider->nsa, $this->csProvider->url);
        $client->setAggHeader();
        $client->provision($params);
        
            /** Connectivity Log **/
            $log = "Sent\n".
                    "Connection Id: ".$conn->external_id."\n".
                    "Action: provision\n".
                    "DateTime: ".date(DATE_RFC822)."\n\n";
             
            Yii::trace($log);
    }
}
