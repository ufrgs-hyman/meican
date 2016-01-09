<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\controllers;

use yii\helpers\Url;
use Yii;
use yii\web\Controller;

use meican\circuits\controllers\services\RequesterClient;
use meican\circuits\Module;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\Reservation;
use meican\topology\models\Port;
use meican\topology\models\Device;
use meican\topology\models\Domain;
use meican\topology\models\Provider;
use meican\base\components\DateUtils;

/**
 * Classe que implementa o módulo SoapServer do protocolo NSI Connection Service Requester 2.0
 * 
 * Recebe mensagens de provedores NSI para criar, alterar ou remover conexões (circuitos).
 *
 * Esta classe NÃO deve extender o RbacControler, pois ela recebe respostas de provedores.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class RequesterController extends Controller {
    
    public $enableCsrfValidation = false;
    
    public function actionIndex() {
        return "";
    }   
    
    public function nsiHeader($params) {
        return "";
    }

    public function actionTest() {
        $req = new RequesterClient;
        $req->setAggHeader();
        $req->querySummary();
    }

    public function dataPlaneStateChange($response) {
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        $conn->setActiveDataStatus($response->dataPlaneStatus->active);
        $conn->save();
        
        return "";
    }
    
    public function messageDeliveryTimeout($response) {
        $params = new \stdClass();
        $params->connectionId = $response->connectionId;
    }
    
    public function reserveConfirmed($responseObject){
        $params = new \stdClass();
        $params->connectionId = $responseObject->connectionId;
        
        $conn = Connection::find()->where(['external_id'=>$params->connectionId])->one();
        $conn->confirmCreatePath();
        
        /** Connectivity Log **/
        $log = "Received\n".
                "Connection Id: ".$params->connectionId."\n".
                "Action: reserveConfirmed\n".
                "DateTime: ".date(DATE_RFC822)."\n\n";
        
        Yii::trace($log);
        
        return "";
    }
    
    //Errro conexào nao possui ID nesse momento
    public function reserveFailed($responseObject){
        $params = new \stdClass();
        $params->connectionId = $responseObject->connectionId;
        $connectionStates = $responseObject->connectionStates;
        $serviceException = $responseObject->serviceException;
        
        $conn = Connection::find()->where(['external_id'=>$params->connectionId])->one();
        $conn->failedCreatePath();
        
        /** Connectivity Log **/
        $log = "Received\n".
               "Connection Id: ".$params->connectionId."\n".
               "Action: reserveFailed\n".
               "DateTime: ".date(DATE_RFC822)."\n\n";
        
        Yii::trace($log);
        
        return "";
    }

    public function reserveCommitConfirmed($responseObject){
        $params = new \stdClass();
        $params->connectionId = $responseObject->connectionId;
        
        /** Connectivity Log **/
        $log = "Received\n".
                "Connection Id: ".$params->connectionId."\n".
                "Action: reserveCommitConfirmed\n".
                "DateTime: ".date(DATE_RFC822)."\n\n";
        
        Yii::trace($log);
        
        $conn = Connection::find()->where(['external_id'=>$params->connectionId])->one();
        $conn->confirmCommit();
        
        return "";
    }
    
    public function querySummaryConfirmed($response) {
        /*$reservation = $response->reservation;
        Yii::trace(print_r($reservation,true));
        foreach ($reservation as $connection) {
            $conn = Connection::find()->where(['external_id'=>$connection->connectionId])->one();
            if (!$conn) {
                $res = new Reservation;
                $res->type = Reservation::TYPE_NORMAL;
                $res->name = $connection->description;
                $res->date = DateUtils::now();
                $res->start = DateUtils::toUTCfromGMT($connection->criteria->schedule->startTime);
                $res->finish = DateUtils::toUTCfromGMT($connection->criteria->schedule->endTime);
                $res->bandwidth = 1;
                $res->requester_nsa = str_replace("urn:ogf:network:", "", $connection->requesterNSA);
                $res->provider_nsa = "1";
                $res->save();

                $conn = new Connection;
        
                $conn->start = DateUtils::toUTCfromGMT($connection->criteria->schedule->startTime);
                $conn->finish = DateUtils::toUTCfromGMT($connection->criteria->schedule->endTime);
                $conn->external_id = $connection->connectionId;
                $conn->reservation_id = $res->id;
                $conn->status = Connection::STATUS_PROVISIONED;
                $conn->dataplane_status = Connection::DATA_STATUS_INACTIVE;
                $conn->auth_status = Connection::AUTH_STATUS_APPROVED;
                $conn->save();

                $pathNodes = $connection->criteria->children->child;
                if (count($pathNodes) < 2) {
                    $pathNodes = [$pathNodes];
                }
                
                Yii::trace(print_r($pathNodes,true));

                $i = 0;
                
                foreach ($pathNodes as $pathNode) {
                    Yii::trace(print_r($pathNode,true));
                    
                    $pathNodeXml = $pathNode->any;
                    $pathNodeXml = str_replace("<nsi_p2p:p2ps>","<p2p>", $pathNodeXml);
                    $pathNodeXml = str_replace("</nsi_p2p:p2ps>","</p2p>", $pathNodeXml);
                    $pathNodeXml = '<?xml version="1.0" encoding="UTF-8"?>'.$pathNodeXml;
                    $xml = new \DOMDocument();
                    $xml->loadXML($pathNodeXml);
                    $parser = new \DOMXpath($xml);
                    $src = $parser->query("//sourceSTP");
                    $dst = $parser->query("//destSTP");
                        
                    $path = new ConnectionPath;
                    $path->conn_id = $conn->id;
                    $path->path_order = $i;
                    $i++;
                        
                    $path->setPortBySTP($src->item(0)->nodeValue);
                    $path->setDomainBySTP($src->item(0)->nodeValue);

                    if(!$path->save()) {
                        Yii::trace($path);
                        return false;
                    }

                    $path = new ConnectionPath;
                    $path->conn_id = $conn->id;
                    $path->path_order = $i;
                    $i++;
                        
                    $path->setPortBySTP($dst->item(0)->nodeValue);
                    $path->setDomainBySTP($dst->item(0)->nodeValue);

                    if(!$path->save()) {
                        Yii::trace($path);
                        return false;
                    }
                }
            }
        }*/
        $conn = Connection::find()->where(['external_id'=>$response->reservation->connectionId])->one();

        if ($conn) {
            if($conn->status == Connection::STATUS_SUBMITTED) {

                if($this->saveConnPath($conn, $response)) {
                    $conn->confirmSummary();
                
                } else {
                    
                    /////Path invalido
                    /////Inconsistencias na topologia
                }

            //atualizar info da reserva
            } else {
                $conn->setActiveDataStatus($response->reservation->connectionStates->dataPlaneStatus->active);
                $conn->save();
            }
        }
    }
    
    private function saveConnPath($conn, $response) {
        //clean old paths
        $oldPaths = $conn->getPaths()->all();
        foreach ($oldPaths as $oldPath) {
            $oldPath->delete();
        }

        //save new paths
        $pathNodes = $response->reservation->criteria->children->child;
        if (count($pathNodes) < 2) {
            $pathNodes = [$pathNodes];
        }
        
        Yii::trace(print_r($pathNodes,true));

        $i = 0;
        
        foreach ($pathNodes as $pathNode) {
            Yii::trace(print_r($pathNode,true));
            
            $pathNodeXml = $pathNode->any;
            $pathNodeXml = str_replace("<nsi_p2p:p2ps>","<p2p>", $pathNodeXml);
            $pathNodeXml = str_replace("</nsi_p2p:p2ps>","</p2p>", $pathNodeXml);
            $pathNodeXml = '<?xml version="1.0" encoding="UTF-8"?>'.$pathNodeXml;
            $xml = new \DOMDocument();
            $xml->loadXML($pathNodeXml);
            $parser = new \DOMXpath($xml);
            $src = $parser->query("//sourceSTP");
            $dst = $parser->query("//destSTP");
                
            $path = new ConnectionPath;
            $path->conn_id = $conn->id;
            $path->path_order = $i;
            $i++;
                
            $path->setPortBySTP($src->item(0)->nodeValue);
            $path->setDomainBySTP($src->item(0)->nodeValue);

            if(!$path->save()) {
                Yii::trace($path);
                return false;
            }

            $path = new ConnectionPath;
            $path->conn_id = $conn->id;
            $path->path_order = $i;
            $i++;
                
            $path->setPortBySTP($dst->item(0)->nodeValue);
            $path->setDomainBySTP($dst->item(0)->nodeValue);

            if(!$path->save()) {
                Yii::trace($path);
                return false;
            }
        }
        
        return true;
    }
    
    public function reserveCommitFailed($responseObject){
        $params = new \stdClass();
        $params->connectionId = $responseObject->connectionId;
        $connectionStates = $responseObject->connectionStates;
        $serviceException = $responseObject->serviceException;
        
        $conn = Connection::find()->where(['external_id'=>$params->connectionId])->one();
        $conn->failedCommit();
        
        /** Connectivity Log **/
        $log = "Received\n".
                "Connection Id: ".$params->connectionId."\n".
                "Action: reserveCommitFailed\n".
                "DateTime: ".date(DATE_RFC822)."\n\n";
        
        Yii::trace($log);
        
        return "";
    }
                
    public function provisionConfirmed($responseObject){
        $params = new \stdClass();
        $params->connectionId = $responseObject->connectionId;
        
        $conn = Connection::find()->where(['external_id'=>$params->connectionId])->one();
        $conn->confirmProvision();
        
        /** Connectivity Log **/
        $log = "Received\n".
                "Connection Id: ".$params->connectionId."\n".
                "Action: provisionConfirmed\n".
                "DateTime: ".date(DATE_RFC822)."\n\n";
        
        Yii::trace($log);
        
        return "";
    }
    
    public function terminateConfirmed($responseObject){
        $params = new \stdClass();
        $params->connectionId = $responseObject->connectionId;
        
        $conn = Connection::find()->where(['external_id'=>$params->connectionId])->one();
        $conn->confirmCancel();
        
        /** Connectivity Log **/
        $log = "Received\n".
                "Connection Id: ".$params->connectionId."\n".
                "Action: terminateConfirmed\n".
                "DateTime: ".date(DATE_RFC822)."\n\n";
            
        Yii::trace($log);
        
        return "";
    }
}

$wsdl = Url::to('@web/wsdl/ogf_nsi_connection_requester_v2_0.wsdl', true);

$requester = new \SoapServer($wsdl, array('encoding'=>'UTF-8'));
$requester->setObject(new RequesterController('req', Module::getInstance()));
$requester->handle();
    
?>