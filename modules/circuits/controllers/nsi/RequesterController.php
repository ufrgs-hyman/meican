<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\controllers\nsi;

use yii\helpers\Url;
use Yii;
use yii\web\Controller;

use meican\circuits\services\NSIRequester;
use meican\circuits\Module;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionEvent;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\Reservation;
use meican\topology\models\Port;
use meican\topology\models\Device;
use meican\topology\models\Domain;
use meican\topology\models\Provider;
use meican\base\utils\DateUtils;
use meican\nsi\ConnectionRequesterServer;

/**
 * Classe que implementa o módulo SoapServer do protocolo NSI Connection Service Requester 2.0
 * 
 * Recebe mensagens de provedores NSI para criar, alterar ou remover conexões (circuitos).
 *
 * Esta classe NÃO deve extender o RbacControler, pois ela recebe respostas de provedores.
 *
 * @author Maurício Quatrin Guerreiro
 */
class RequesterController extends Controller implements ConnectionRequesterServer {

    public $layout = "@meican/base/views/layouts/blank";
    
    public $enableCsrfValidation = false;

    public function actionIndex() {
        return "";
    }   
    
    public function nsiHeader($response) {
        return "";
    }

    public function dataPlaneStateChange($response) {
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";

        $conn->buildEvent(ConnectionEvent::TYPE_NSI_DATAPLANE_CHANGE, Yii::$app->request->getRawBody())->save();
        $conn->setActiveDataStatus($response->dataPlaneStatus->active)->save();
        if($conn->status == Connection::STATUS_WAITING_DATAPLANE) {
            $conn->requestProvision();
        }
        
        return '';
    }
    
    public function messageDeliveryTimeout($response) {
        return "";
    }
    
    public function reserveConfirmed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";

        $conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_CONFIRMED, Yii::$app->request->getRawBody())->save();
        $conn->confirmResources();
        return "";
    }
    
    public function reserveFailed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";

        $conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_FAILED, Yii::$app->request->getRawBody())->save();
        $conn->failedResources();
        return "";
    }

    public function reserveTimeout($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";

        $conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_TIMEOUT, Yii::$app->request->getRawBody())->save();
        $conn->failedCreate();
        return "";
    }

    public function reserveAbortConfirmed($response) {
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_ABORT_CONFIRMED, Yii::$app->request->getRawBody())->save();
        return "";
    }

    public function reserveCommitConfirmed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT_CONFIRMED, Yii::$app->request->getRawBody())->save();
        $conn->confirmCommit();
        return "";
    }

    public function reserveCommitFailed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT_FAILED, Yii::$app->request->getRawBody())->save();
        $conn->failedCommit();
        return "";
    }
                
    public function provisionConfirmed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_PROVISION_CONFIRMED, Yii::$app->request->getRawBody())->save();
        $conn->confirmProvision();
        return "";
    }
    
    public function terminateConfirmed($response){
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_TERMINATE_CONFIRMED, Yii::$app->request->getRawBody())->save();
        $conn->confirmCancel();
        return "";
    }

    public function releaseConfirmed($response) {
        $conn = Connection::find()->where(['external_id'=>$response->connectionId])->one();
        if(!$conn) return "";
        $conn->buildEvent(ConnectionEvent::TYPE_NSI_RELEASE_CONFIRMED, Yii::$app->request->getRawBody())->save();
        $conn->confirmRelease();
        return "";
    }

    public function queryRecursiveConfirmed($response) {
        return "";
    }

    public function queryNotificationConfirmed($response) {
        return "";
    }

    public function queryResultConfirmed($response) {
        return "";
    }

    public function error($response) {
        return "";
    }

    public function errorEvent($response) {
        return "";
    }

    private function updateAllConnections($response) {
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
    }
    
    public function querySummaryConfirmed($response) {
        $conn = Connection::find()->where(['external_id'=>$response->reservation->connectionId])->one();
        if(!$conn) return "";

        $conn->buildEvent(ConnectionEvent::TYPE_NSI_SUMMARY_CONFIRMED, Yii::$app->request->getRawBody())->save();
        
        if($conn->version != $response->reservation->criteria->version) {
            $conn->start = (new \DateTime($response->reservation->criteria->schedule->startTime))->format("Y-m-d H:i:s");
            $conn->finish = (new \DateTime($response->reservation->criteria->schedule->endTime))->format("Y-m-d H:i:s");

            if($conn->version == 0) {
                if($this->updateConnectionPath($conn, $response)) {
                    //Como eh a primeira versao deve ser verificado se ha autorizacao para este circuito.
                    $conn->confirmRead();
                } else {
                    /////Path invalido
                    /////Inconsistencias na topologia
                    Yii::trace("path invalid?");
                }
            } else {
                $conn->status = Connection::STATUS_WAITING_DATAPLANE;
            }

            $conn->version = $response->reservation->criteria->version;
        } 
            
        $conn->setActiveDataStatus($response->reservation->connectionStates->dataPlaneStatus->active);
        $conn->save();
        return "";
    }
    
    private function updateConnectionPath($conn, $response) {
        //updating path

        //clean old points
        $oldPaths = $conn->getPaths()->all();
        foreach ($oldPaths as $oldPath) {
            $oldPath->delete();
        }

        //save new points
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