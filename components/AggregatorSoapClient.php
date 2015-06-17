<?php

namespace app\components;

use yii\helpers\Url;
use Yii;

class AggregatorSoapClient extends \SoapClient {
	
	public $wsdl;
	public $replyTo;
	public $local_cert;
	public $cert_passphrase;
	public $providerNSA;

	function __construct($providerNSA, $connectionService){
		$this->wsdl = $connectionService."?wsdl";
		if (Yii::$app->id == "meican-console") {
			$meicanRequesterUrl = Yii::$app->params['meican.connection.requester.url'];
		} else {
			$meicanRequesterUrl = Url::toRoute("/circuits/connection", "http");
		}
		
		$this->replyTo = $meicanRequesterUrl;
		$this->local_cert = realpath(__DIR__."/../certificates/".\Yii::$app->params['meican.certificate.filename']);
		$this->cert_passphrase = Yii::$app->params['meican.certificate.passphrase'];
		$this->providerNSA = $providerNSA;
		
		$soapOptions = array(
				"local_cert" => $this->local_cert,
				"passphrase" => $this->cert_passphrase,
				"cache_wsdl" => WSDL_CACHE_NONE,
				"trace" => 1
		);
		
		parent::__construct($this->wsdl, $soapOptions);
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
		$connection = new \SoapVar(array("Connection" => Yii::$app->params['meican.nsa.id']), SOAP_ENC_OBJECT, null, null, null, null);

		$headerBody = array(
				"protocolVersion"=>"application/vnd.ogf.nsi.cs.v2.provider+soap",
				"correlationId"  =>"", //Generated on request
				"requesterNSA"   =>	Yii::$app->params['meican.nsa.id'],
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
}