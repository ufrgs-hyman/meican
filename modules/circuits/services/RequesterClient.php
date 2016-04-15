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

/**
 * Classe que implementa o módulo SoapClient do protocolo NSI Connection Service Requester 2.0
 * 
 * Envia mensagens para provedores NSI para criar, alterar  ou remover conexões (circuitos).
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class RequesterClient extends \SoapClient {
    
    public $wsdl;
    public $replyTo;
    public $local_cert;
    public $cert_passphrase;
    public $providerNSA;
    public $conn;
    public $res;

    function __construct($conn=null){
        $this->conn = $conn;
        if ($conn) $this->res = $conn->getReservation()->one();
        $csUrl = null;
        
        $defaultNsa = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_DEFAULT_PROVIDER_NSA)->value;
        if ($this->res && ($this->res->provider_nsa != $defaultNsa)) {
            return new \Exception("Provider enabled is not equal the reservation provider.");
        }

        $csUrl = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_DEFAULT_CS_URL)->value;

        if ($csUrl) {
            $this->wsdl = $csUrl."?wsdl";
        }
        
        if (Yii::$app->id == "meican-console") {
            $meicanRequesterUrl = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_MEICAN_REQUESTER_URL)->value;
        } else {
            $meicanRequesterUrl = Url::toRoute("/circuits/requester", "http");
        }
        
        $this->replyTo = $meicanRequesterUrl;
        $this->local_cert = realpath(__DIR__."/../../../../certificates/".\Yii::$app->params['certificate.filename']);
        $this->cert_passphrase = Yii::$app->params['certificate.pass'];
        $this->providerNSA = "urn:ogf:network:".$defaultNsa;

        if (!$this->isDummy()) {
            $soapOptions = array(
                "local_cert" => $this->local_cert,
                "passphrase" => $this->cert_passphrase,
                "cache_wsdl" => WSDL_CACHE_NONE,
                "trace" => 1
            );
            
            parent::__construct($this->wsdl, $soapOptions);
        } else {
            parent::__construct(null, array('uri' => "http://localhost/", 'location'=> ''));
        }
    }

    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($request);
        
        /** Setting namespaces **/
        $dom->documentElement->setAttribute('xmlns:xs', 'http://www.w3.org/2001/XMLSchema');
        $dom->documentElement->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $dom->documentElement->setAttribute('xmlns:gns', 'http://nordu.net/namespaces/2013/12/gnsbod');
        $dom->documentElement->setAttribute('xmlns:saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
        $dom->documentElement->setAttribute('xmlns:type', 'http://schemas.ogf.org/nsi/2013/12/connection/types');
        $dom->documentElement->setAttribute('xmlns:head', 'http://schemas.ogf.org/nsi/2013/12/framework/headers');
        
        /** Generating Correlation ID */
        $dom->getElementsByTagName("correlationId")->item(0)->nodeValue = 'urn:uuid:'.$this->newGuid();
        
        /** Setting prefixes of the elements **/
        $this->changeTag($dom,"ConnectionTrace", "gns:ConnectionTrace");
        $this->changeTag($dom, "reserve", "type:reserve");
        $this->changeTag($dom, "reserveCommit","type:reserveCommit");
        $this->changeTag($dom, "provision", "type:provision");
        $this->changeTag($dom, "p2ps", "p2p:p2ps");
        
        /** Setting attributes **/
        $this->setAttributeByTag($dom, "Connection", "index", "0");
        $this->setAttributeByTag($dom, "criteria", "version", "1");
        $this->setAttributeByTag($dom, "p2p:p2ps", "xmlns:p2p", "http://schemas.ogf.org/nsi/2013/12/services/point2point");
        $this->setAttributeByTag($dom, "parameter", "type", "protection");
        $this->setEro($dom);
        $request = $dom->saveXML();
        
        Yii::trace("client request: ".$request);
        

        return parent::__doRequest($request, $location, $action, $version);
    }

    function changeTag($dom, $oldTagName, $newTagName, $attributes = array()){
        $newNode = $dom->createElement($newTagName);

        if($oldNode = $dom->getElementsByTagName($oldTagName)->item(0)){

            $childNodes = $oldNode->childNodes;
            foreach($childNodes as $child){
                $newChild = $child->cloneNode(true);
                $newNode->appendChild($newChild);
            }

            $parent = $oldNode->parentNode;
            $parent->replaceChild($newNode, $oldNode);
        }
    }

    function setAttributeByTag($dom, $tagName, $attName, $attValue){
        if($nodes = $dom->getElementsByTagName($tagName)){
            foreach($nodes as $node)
                $node->setAttribute($attName, $attValue);
        }
    }
    
    function setEro($dom){
        if($nodes = $dom->getElementsByTagName("orderedSTP")){
            $i = 0;
            foreach($nodes as $node) {
                $node->setAttribute("order", $i);
                $i++;
            }
        }
    }

    function setAggHeader(){
        $ns = "http://schemas.ogf.org/nsi/2013/12/framework/headers";
        $meicanNsa = "urn:ogf:network:".CircuitsPreference::findOne(CircuitsPreference::MEICAN_NSA)->value;
        $connection = new \SoapVar(array("Connection" => $meicanNsa), 
            SOAP_ENC_OBJECT, null, null, null, null);

        $headerBody = array(
                "protocolVersion"=>"application/vnd.ogf.nsi.cs.v2.provider+soap",
                "correlationId"  =>"", //Generated on request
                "requesterNSA"   => $meicanNsa,
                "providerNSA"    =>$this->providerNSA,
                "replyTo"       => $this->replyTo,
                "ConnectionTrace" => $connection
        );

        $headerBody = new \SoapVar($headerBody, SOAP_ENC_OBJECT, NULL, NULL, NULL, NULL);
        $header = new \SoapHeader($ns, "nsiHeader", $headerBody);

        $this->__setSoapHeaders($header);
    }

    function newGuid() {
        $s = md5(uniqid(rand(),true));
        $guidText =
        substr($s,0,8) . '-' .
        substr($s,8,4) . '-' .
        substr($s,12,4). '-' .
        substr($s,16,4). '-' .
        substr($s,20);
        return $guidText;
    }

    private function isDummy() {
        return Yii::$app->params["provider.force.dummy"];
    }

    public function requestCreate() {
        if ($this->isDummy()) {
            sleep(4);
            $date = new \DateTime();
            $this->conn->external_id = 'f'.$date->format('YmdHis');
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE)->save();
            sleep(1);
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_RESPONSE)->save();
            sleep(1);
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_CONFIRMED)->save();
            $this->conn->confirmCreate();
            $this->conn->confirmCreatePath();
            return;
        } 
        
        $directionality = "Bidirectional";
        $symmetricPath = "true";
        $parameter = ($this->res->protected == 1) ? "PROTECTED" : "UNPROTECTED";
        $serviceType = "http://services.ogf.org/nsi/2013/12/descriptions/EVTS.A-GOLE";
        $firstPath = $this->res->getFirstPath()->one();
        $lastPath = $this->res->getLastPath()->one();
        $sourceSTP = $firstPath->getFullPortUrn()."?vlan=".$firstPath->vlan;
        $destSTP = $lastPath->getFullPortUrn()."?vlan=".$lastPath->vlan;
        
        $date = new \DateTime($this->conn->start);
        $start = $date->format('Y-m-d\TH:i:s.000-00:00');
        
        $date = new \DateTime($this->conn->finish);
        $finish = $date->format('Y-m-d\TH:i:s.000-00:00');
        
        /** Creating the SOAP request **/
        $schedule = array(
                "startTime" => $start, //start_time
                "endTime" => $finish
        );
        
        $paths = $this->res->getPaths()->all();
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
                        "capacity" => $this->res->bandwidth, //Bandwidth
                        "directionality" => $directionality, //Auto
                        "symmetricPath" => $symmetricPath, //Auto
                        "sourceSTP" => $sourceSTP,  //src_urn
                        "destSTP" => $destSTP, //dest_urn
                        "parameter" => $parameter, //Auto
                        $ero
                );
            } else {
                $p2ps = array(
                        "capacity" => $this->res->bandwidth, //Bandwidth
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
        
            $params = [];
            if ($this->res->gri) {
                $params["globalReservationId"] = $this->res->gri;
            }
            $params["description"] = $this->res->name;
            $params["criteria"] = $criteria;

            $this->setAggHeader();
            
            try{
                $this->reserve($params);
            }catch(\SoapFault $error){
                $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE, $this->__getLastRequest())->save();
                Yii::trace($error);
                return false;
            }

            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE, $this->__getLastRequest())->save();
        
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $response = $this->__getLastResponse();
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_RESPONSE, $response)->save();
            
            Yii::trace($response);
            $dom->loadXML($response);
        
            $connectionId = $dom->getElementsByTagName("connectionId")->item(0);
            $connectionId = $connectionId->textContent;
        
            if(isset($connectionId)){
                $this->conn->external_id = $connectionId;
                $this->conn->confirmCreate();
                    
                /** Connectivity Log **/
                $log = "Sent\n".
                        "Connection Id: ".$connectionId."\n".
                        "Action: reserve\n".
                        "DateTime: ".date(DATE_RFC822)."\n\n";
        
                Yii::trace($log);
                    
            }
            else {
                $this->conn->failedCreate();
                Yii::trace("Failed");
                return FALSE;
            }
            return TRUE;
    }
    
    public function requestCancel() {
        if ($this->isDummy()) {
            $this->conn->confirmCancel();
            return;
        }
        
        $params = array(
                "connectionId" => $this->conn->external_id
        );
         
        $this->setAggHeader();
        
        try{
            $this->terminate($params);
            Yii::trace($this->__getLastResponse());
        }catch(SoapFault $error){
            return false;
        }
    }
    
    public function requestSummary() {
        if ($this->isDummy()) {
            sleep(1);
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_SUMMARY)->save();
            sleep(1);
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_SUMMARY_CONFIRMED)->save();
            $this->conn->confirmSummary();
            return;
        }
        
        $params = array(
                "connectionId" => $this->conn->external_id
        );
        
        $this->setAggHeader();
        $this->querySummary($params);
        
            /** Connectivity Log **/
            $log = "Sent\n".
                    "Connection Id: ".$this->conn->external_id."\n".
                    "Action: querySummary\n".
                    "DateTime: ".date(DATE_RFC822)."\n\n";
                
            Yii::trace($log);
        
    }
    
    public function requestCommit() {
        if ($this->isDummy()) {
            sleep(1);
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT)->save();
            sleep(1);
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT_CONFIRMED)->save();
            $this->conn->confirmCommit();
            return;
        }
        
        $params = array(
                "connectionId" => $this->conn->external_id
        );
        
        $this->setAggHeader();
        $this->reserveCommit($params);
        
            /** Connectivity Log **/
            $log = "Sent\n".
                    "Connection Id: ".$this->conn->external_id."\n".
                    "Action: reserveCommit\n".
                    "DateTime: ".date(DATE_RFC822)."\n\n";
        
            Yii::trace($log);
    }
    
    public function requestProvision() {
        if ($this->isDummy()) {
            sleep(1);
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_PROVISION)->save();
            sleep(1);
            $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_PROVISION_CONFIRMED)->save();
            $this->conn->confirmProvision();
            return;
        }
        
        $params = array(
                "connectionId" => $this->conn->external_id
        );
         
        $this->setAggHeader();
        $this->provision($params);
        
            /** Connectivity Log **/
            $log = "Sent\n".
                    "Connection Id: ".$this->conn->external_id."\n".
                    "Action: provision\n".
                    "DateTime: ".date(DATE_RFC822)."\n\n";
             
            Yii::trace($log);
    }
}