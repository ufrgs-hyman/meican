<?php
//ESTRUTURA DA TOPOLOGIA
/*
 array(7) {
  ["cipo.pop-sp.rnp.br"]=>
  array(1) {
    ["devices"]=>
    array(1) {
      ["vlsr1"]=>
      array(1) {
        ["urns"]=>
        array(7) {
          ["urn:ogf:network:domain=cipo.pop-sp.rnp.br:node=vlsr1:port=5:link=*"]=>
          array(6) {
            ["port"]=>
            string(1) "5"
            ["cap"]=>
            string(10) "1000000000"
            ["capMax"]=>
            string(10) "1000000000"
            ["capMin"]=>
            string(8) "10000000"
            ["granu"]=>
            string(8) "10000000"
            ["vlan"]=>
            string(9) "0,700-800"
          }
        }
      }
    }
  }
  ["cipo.pop-go.rnp.br"]=>
  array(1) {
    ["devices"]=>
    array(1) {
      ["vlsr01"]=>
      array(1) {
        ["urns"]=>
        array(10) {
          ["urn:ogf:network:domain=cipo.pop-go.rnp.br:node=vlsr01:port=2:link=*"]=>
          array(6) {
            ["port"]=>
            string(1) "2"
            ["cap"]=>
            string(10) "1000000000"
            ["capMax"]=>
            string(10) "1000000000"
            ["capMin"]=>
            string(9) "100000000"
            ["granu"]=>
            string(8) "10000000"
            ["vlan"]=>
            string(9) "0,700-799"
          }
 */

namespace app\modules\topology\models;

use app\components\PerfsonarSoapClient;

class PerfsonarTopology {

	private $topology = array();
	private $errors = array();
	private $xpath;
	private $url;
	
	function __construct($discoveryUrl){
		$this->url = $discoveryUrl;
	}

	function loadXml($input) {
		$xml = new \SimpleXMLElement($input);
		$namespaces = $xml->getNameSpaces(true);
		$xml = new \DOMDocument();
		$xml->loadXML($input);
		$this->xpath = new \DOMXpath($xml);
			
		foreach ($namespaces as $ns) {
			$this->xpath->registerNamespace('x', $ns);

			$this->parseDomains();
		}
	}

	function loadFromDiscovery() {
		$soapClient = new PerfsonarSoapClient($this->url);

		$this->loadXml($soapClient->getTopology());
	}

	function getData() {
		return $this->topology;
	}

	function getErrors() {
		return $this->errors;
	}

	function addURN($domainName, $deviceName, $urn,	$portName, $capMax, $capMin, $granu,
			$vlanRange) {
		$deviceName = strtolower($deviceName);
		$this->topology[$domainName]["devices"][$deviceName]["urns"][$urn] = array();
		$this->topology[$domainName]["devices"][$deviceName]["urns"][$urn]["port"] = $portName;
		$this->topology[$domainName]["devices"][$deviceName]["urns"][$urn]["capMax"] = $capMax;
		$this->topology[$domainName]["devices"][$deviceName]["urns"][$urn]["capMin"] = $capMin;
		$this->topology[$domainName]["devices"][$deviceName]["urns"][$urn]["granu"] = $granu;
		$this->topology[$domainName]["devices"][$deviceName]["urns"][$urn]["vlan"] = $vlanRange;
	}

	function parseDomains() {
		$domainNodes = $this->xpath->query("//x:domain");
		if ($domainNodes) {
			foreach ($domainNodes as $domainNode) {
				$idString = $domainNode->getAttribute('id');
				$id = explode("=", $idString);
				$domainName = $id[count($id)-1];
					
				$this->parseDevices($domainNode, $domainName);
			}
		}
	}

	function parseDevices($domainNode, $domainName) {
		$deviceNodes = $this->xpath->query(".//x:node", $domainNode);
		if($deviceNodes) {
			foreach ($deviceNodes as $deviceNode) {
				$idString = $deviceNode->getAttribute('id');
				$id = explode("=", $idString);
				$deviceName = $id[count($id)-1];

				$this->parsePorts($domainName, $deviceNode, $deviceName);
			}
		}
	}

	function parsePorts($domainName, $deviceNode, $deviceName) {
		$portNodes = $this->xpath->query(".//x:port", $deviceNode);
		if($portNodes) {
			foreach ($portNodes as $portNode) {
				$idString = $portNode->getAttribute('id');
				$id = explode("=", $idString);

				$portName = $id[count($id)-1];

				$value = $this->xpath->query(".//x:maximumReservableCapacity", $portNode);
				$capMax = $value->item(0) ? $value->item(0)->nodeValue : null;
				$value = $this->xpath->query(".//x:minimumReservableCapacity", $portNode);
				$capMin = $value->item(0) ? $value->item(0)->nodeValue : null;
				$value = $this->xpath->query(".//x:granularity", $portNode);
				$granu = $value->item(0) ? $value->item(0)->nodeValue : null;

				$this->parseLinks($domainName, $deviceName, $portNode, $portName,
						$capMax, $capMin, $granu);
			}
		}
	}

	function parseLinks($domainName, $deviceName, $portNode, $portName, $capMax, $capMin,
			$granu) {
		$linkNodes = $this->xpath->query(".//x:link", $portNode);
		if($linkNodes) {
			foreach ($linkNodes as $linkNode) {
				$urn = $linkNode->getAttribute('id');

				if (!$capMax) {
					$value = $this->xpath->query(".//x:maximumReservableCapacity", $linkNode);
					$capMax = $value->item(0) ? $value->item(0)->nodeValue : null;
				}

				if (!$capMin) {
					$value = $this->xpath->query(".//x:minimumReservableCapacity", $linkNode);
					$capMin = $value->item(0) ? $value->item(0)->nodeValue : null;
				}

				if (!$granu) {
					$value = $this->xpath->query(".//x:granularity", $linkNode);
					$granu = $value->item(0) ? $value->item(0)->nodeValue : null;
				}

				$value = $this->xpath->query(".//x:vlanRangeAvailability", $linkNode);
				$vlanRange = $value->item(0) ? $value->item(0)->nodeValue : null;

				$this->addURN(
						$domainName,
						$deviceName,
						$urn,
						$portName,
						$capMax,
						$capMin,
						$granu,
						$vlanRange);
			}
		}
	}
}

?>