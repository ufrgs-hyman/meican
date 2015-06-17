<?php

namespace app\components;

class PerfsonarSoapClient extends \SoapClient {
	
	private $serverUrl;
	
	function __construct($serverUrl){
		$this->serverUrl = $serverUrl;
		$soapOptions = array(
				'location' => $this->serverUrl,
				'uri'      => "",
		);
		
		parent::__construct(null, $soapOptions);
	}
	
	function getTopology() {
		$request = "<?xml version='1.0' encoding='UTF-8'?>".
			"<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' ".
			"xmlns:xsd='http://www.w3.org/2001/XMLSchema' ".
			"xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>".
			"<soapenv:Header>".
			"<SOAPAction></SOAPAction>".
			"</soapenv:Header>".
			"<soapenv:Body>".    
			"<nmwg:message type='TSQueryRequest' id='msg1' ".
			"xmlns:nmwg='http://ggf.org/ns/nmwg/base/2.0/' ".
			"xmlns:xquery='http://ggf.org/ns/nmwg/tools/org/perfsonar/service/lookup/xquery/1.0/'>".
			"<nmwg:metadata id='meta1'>".
			"<nmwg:eventType>http://ggf.org/ns/nmwg/topology/20070809</nmwg:eventType>".
			"</nmwg:metadata>".
			"<nmwg:data metadataIdRef='meta1' id='d1' />".
			"</nmwg:message>".
			"</soapenv:Body></soapenv:Envelope>";
	
		//header("Content-Type:text/xml");
		//echo $request;
		//die();
		
		return parent::__doRequest(
				$request, 
				$this->serverUrl, 
				"", 
				1);
	}
}