<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\controllers\nsi;

use yii\helpers\Url;
use Yii;
use yii\web\Controller;

use meican\circuits\services\NSIRequester;
use meican\circuits\Module;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\Reservation;
use meican\topology\models\Port;
use meican\topology\models\Device;
use meican\topology\models\Domain;
use meican\topology\models\Provider;
use meican\base\components\DateUtils;
use meican\nsi\connection\RequesterServer;

/**
 * Classe que implementa o módulo SoapServer do protocolo NSI Connection Service Requester 2.0
 * 
 * Recebe mensagens de provedores NSI para criar, alterar ou remover conexões (circuitos).
 *
 * Esta classe NÃO deve extender o RbacControler, pois ela recebe respostas de provedores.
 *
 * @author Maurício Quatrin Guerreiro
 */
class RequesterController extends Controller implements RequesterServer {
    
    public $enableCsrfValidation = false;
    
    public function actionIndex() {
        return "";
    }   
    
    public function nsiHeader($response) {
        return "";
    }

    public function dataPlaneStateChange($response) {
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_DATAPLANE_CHANGE, $response)->save();
        $conn->setActiveDataStatus($response->dataPlaneStatus->active)->save();
    }
    
    public function messageDeliveryTimeout($response) {
        return "";
    }
    
    public function reserveConfirmed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_CONFIRMED, $response)->save();
        $conn->confirmResources();
        return "";
    }
    
    public function reserveFailed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_FAILED, $response)->save();
        $conn->failedResources();
        return "";
    }

    public function reserveTimeout($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_TIMEOUT, $response)->save();
        $conn->failedCreate();
        return "";
    }

    public function reserveCommitConfirmed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT_CONFIRMED, $response)->save();
        $conn->confirmCommit();
        return "";
    }

    public function reserveCommitFailed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT_FAILED, $response)->save();
        $conn->failedCommit();
        return "";
    }
                
    public function provisionConfirmed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_PROVISION_CONFIRMED, $response)->save();
        $conn->confirmProvision();
        return "";
    }
    
    public function terminateConfirmed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_TERMINATE_CONFIRMED, $response)->save();
        $conn->confirmCancel();
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
                    $conn->confirmInfo();
                
                } else {
                    
                    /////Path invalido
                    /////Inconsistencias na topologia
                }

            //atualizar info da reserva
            } else {
                $conn->setActiveDataStatus($response->reservation->connectionStates->dataPlaneStatus->active)->save();
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
}

$wsdl = Url::to('@web/wsdl/ogf_nsi_connection_requester_v2_0.wsdl', true);

$requester = new \SoapServer($wsdl, array('encoding'=>'UTF-8'));
$requester->setObject(new RequesterController('req', Module::getInstance()));
$requester->handle();
    
?>