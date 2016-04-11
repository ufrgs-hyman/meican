<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\services;

use yii\helpers\Url;
use Yii;

use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\ConnectionEvent;
use meican\circuits\models\Provider;
use meican\circuits\models\CircuitsPreference;
use meican\nsi\connection\RequesterClient;

/**
 * Serviço que realiza requisições de circuitos em um ambiente NSI.
 *
 * @author Maurício Quatrin Guerreiro
 */
class NSIRequester implements Requester {
    
    public $conn;
    private $soapClient;

    function __construct($conn){
        $this->conn = $conn;
        //if ($conn) $this->res = $conn->getReservation()->one();
        
        /*$defaultNsa = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_DEFAULT_PROVIDER_NSA)->value;
        if ($this->res && ($this->res->provider_nsa != $defaultNsa)) {
            return new \Exception("Provider enabled is not equal the reservation provider.");
        }*/

        if (Yii::$app->id == "meican-console") {
            $meicanRequesterUrl = CircuitsPreference::findOneValue(CircuitsPreference::CIRCUITS_MEICAN_REQUESTER_URL);
        } else {
            $meicanRequesterUrl = Url::toRoute("/circuits/requester", "http");
        }

        $this->soapClient = new RequesterClient(
            "urn:ogf:network:".CircuitsPreference::findOneValue(CircuitsPreference::MEICAN_NSA),
            $meicanRequesterUrl,
            "urn:ogf:network:".CircuitsPreference::findOneValue(CircuitsPreference::CIRCUITS_DEFAULT_PROVIDER_NSA),
            CircuitsPreference::findOneValue(CircuitsPreference::CIRCUITS_DEFAULT_CS_URL),
            realpath(__DIR__."/../../../../certificates/".\Yii::$app->params['certificate.filename']),
            Yii::$app->params['certificate.pass']);
    }

    public function create() {
        $path = [];
        foreach ($this->conn->getPaths()->all() as $point) {
            $path[] = $point->getFullPortUrn()."?vlan=".$point->vlan;
        }

        $this->soapClient->requestReserve(
            null,
            '1',
            $this->conn->bandwidth,
            new \DateTime($this->conn->start, new \DateTimeZone("UTC")),
            new \DateTime($this->conn->finish, new \DateTimeZone("UTC")),
            $path,
            $this->conn->getReservation()->asArray()->select(['name'])->one()['name']
        );
        
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE, $this->soapClient->__getLastRequest())->save();

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $response = $this->soapClient->__getLastResponse();
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_RESPONSE, $response)->save();
        
        Yii::trace($response);
        $dom->loadXML($response);
    
        $connectionId = $dom->getElementsByTagName("connectionId")->item(0);
        $connectionId = $connectionId->textContent;
    
        if(isset($connectionId)){
            $this->conn->external_id = $connectionId;
            $this->conn->confirmCreate();
        }
        else {
            $this->conn->failedCreate();
            return false;
        }
        return true;
    }

    public function commit() {
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT)->save();
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT_CONFIRMED)->save();
        $this->conn->confirmCommit();
    }

    public function info() {
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_SUMMARY)->save();
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_SUMMARY_CONFIRMED)->save();
        $this->conn->confirmSummary();
    }

    public function update() {
        //verificar o que mudou, senao sempre vai ser reiniciado o circuito.
        //criar data em event, onde podemos guardar alguns dados como o que o usuario alterou quando ele efetuou um update
        $this->soapClient->requestReserve(
            $this->conn->external_id,
            $this->conn->version,
            $this->conn->bandwidth,
            new \DateTime($this->conn->start, new \DateTimeZone("UTC")),
            new \DateTime($this->conn->finish, new \DateTimeZone("UTC"))
        );
    }

    public function provision() {
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_PROVISION)->save();
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_PROVISION_CONFIRMED)->save();
        $this->conn->confirmProvision();
    }
}