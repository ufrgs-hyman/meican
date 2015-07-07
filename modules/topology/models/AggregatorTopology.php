<?php

namespace app\modules\topology\models;

//ESTRUTURA DA TOPOLOGIA
/*
 * array(23) {
  ["jgn-x.jp"]=>
  array(3) {
  	["nsa"] =>
  		[
  			"urn:ogf...." => [
  				"type" => AGGREGATOR,
  				"connection" => "https://sdsds....",
  				"discovery" => "http://...",
  			]
  		]
    ["longitude"]=>
    string(7) "139.764"
    ["latitude"]=>
    string(6) "35.688"
    ["devices"]=>
    array(7) {
      ["bi-ps"]=>
      array(1) {
        ["urns"]=>
        array(1) {
          ["urn:ogf:network:jgn-x.jp:2013:bi-ps"]=>
          array(2) {
            ["port"]=>
            string(0) ""
            ["vlan"]=>
            string(9) "1779-1799"
          }
        }
      }
    }
  }
  ["manlan.internet2.edu"]=>
  array(3) {
    ["longitude"]=>
    string(7) "-74.003"
    ["latitude"]=>
    string(9) "40.718666"
    ["devices"]=>
    array(4) {
      ["es"]=>
      array(1) {
        ["urns"]=>
        array(1) {
          ["urn:ogf:network:manlan.internet2.edu:2013:es"]=>
          array(2) {
            ["port"]=>
            string(0) ""
            ["vlan"]=>
            string(19) "1779-1799,3400-3499"
          }
        }
      }
   }
 */
class AggregatorTopology {

	private $topology = array();
	private $errors = array();
	public $sourceProvider = [];
	private $xpath;
	private $url;
	private $cert_file;
	private $cert_password;

	function __construct($discoveryUrl, $certName, $certPass){
		$this->url = $discoveryUrl;
		$this->cert_password = $certPass;
		$this->cert_file = realpath(__DIR__."/../../../certificates/".$certName);
	}

	function loadXml($input) {
		if ($input == '') throw new \Exception('412');
		$xml = new \SimpleXMLElement($input);
		$namespaces = $xml->getNameSpaces(true);
		$xml = new \DOMDocument();
		$xml->loadXML($input);
		$this->xpath = new \DOMXpath($xml);

		foreach ($namespaces as $ns) {
			$this->xpath->registerNamespace('x', $ns);

			$this->parseDomains();
			$this->parseProviderData();
		}
	}

	function loadFromDiscovery() {
		$ch = curl_init();

		$options = array(
				CURLOPT_RETURNTRANSFER => true,
				//CURLOPT_HEADER         => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,

				CURLOPT_USERAGENT => 'Meican',
				//CURLOPT_VERBOSE        => true,
				CURLOPT_URL => $this->url,
				CURLOPT_SSLCERT => $this->cert_file,
				CURLOPT_SSLCERTPASSWD => $this->cert_password,
		);

		curl_setopt_array($ch , $options);

		$output = curl_exec($ch);
		curl_close($ch);
	//	echo $output;
	$this->loadXml($output);
	}
	
	function getData() {
		return $this->topology;
	}

	function getErrors() {
		return $this->errors;
	}
	
	function addProviderData($domainName, $nsa, $type, $connection, $discovery, $lat, $lng) {
		$this->topology["domain"][$domainName]["nsa"][$nsa]["type"] = $type;
		$this->topology["domain"][$domainName]["nsa"][$nsa]["connection"] = $connection;
		$this->topology["domain"][$domainName]["nsa"][$nsa]["discovery"] = $discovery;
		$this->topology["domain"][$domainName]["latitude"] = $lat;
		$this->topology["domain"][$domainName]["longitude"] = $lng;
		
		if ($this->url == $discovery) {
			$this->sourceProvider["nsa"] = $nsa;
			$this->sourceProvider["type"] = $type;
			$this->sourceProvider["connection"] = $connection;
			$this->sourceProvider["discovery"] = $discovery;
		}
	}

	function addURN($domainName, $deviceName, $urn, $portName, $portId, $vlan) {
		$portName = strtolower($portName);
		
		if (isset($this->topology[$domainName]["devices"][$deviceName]["urns"][$urn])) {
			return;
		}
		
		$this->topology["domain"][$domainName]["devices"][$deviceName]["urns"][$urn] = array();
		$this->topology["domain"][$domainName]["devices"][$deviceName]["urns"][$urn]["port"] = $portName;
		$this->topology["domain"][$domainName]["devices"][$deviceName]["urns"][$urn]["vlan"] = $vlan;
		$this->topology["domain"][$domainName]["devices"][$deviceName]["urns"][$urn]["portId"] = $portId;
	}
	
	function addAlias($urn, $portId) {
		$this->topology["sdp"][$portId] = $urn;
	}
	
	function parseDomains() {
		$domainNodes = $this->xpath->query("//x:Topology");
		if ($domainNodes) {
			foreach ($domainNodes as $domainNode) {
				$idString = $domainNode->getAttribute('id');
				$id = explode(":", $idString);
				//         0   1     2         3        4    5
				//	      urn:ogf:network:cipo.rnp.br:2014::rjo-1

				$domainName = $id[3];

				$this->parseDevices($domainNode, $domainName);
			}
		}
	}

	function parseDevices($domainNode, $domainName) {
		$deviceNodes = $this->xpath->query(".//x:BidirectionalPort", $domainNode);
		if($deviceNodes) {
			foreach ($deviceNodes as $deviceNode) {
				$urn = $deviceNode->getAttribute('id');
				$id = explode(":", $urn);

				if ($id[0] !== "urn") {
					$this->errors["Unknow URN"][] = $urn;
					continue;
				}

				if ($id[5] == "topology" || $id[5] == "") {
					$devicePort = array_slice($id, 6);
				} else {
					$devicePort = array_slice($id, 5);
				}
				
				if (count($devicePort) > 2) {
					$device = $devicePort[0];
					array_shift($devicePort);
					$devicePort = [$device, implode(":", $devicePort)];
				}
				
				$this->parseDevicePort($domainNode, $domainName, $deviceNode, $devicePort, $urn);
			}
		}
	}

	function parseDevicePort($domainNode, $domainName, $deviceNode, $devicePort, $urn) {
		$portGroupNodes = $this->xpath->query(".//x:PortGroup", $deviceNode);
		if($portGroupNodes) {
			foreach ($portGroupNodes as $portGroupNode) {
				$portGroupId = $portGroupNode->getAttribute('id');

				$id = explode(":", $portGroupId);
				if ($id[0] !== "urn") {
					$this->errors["Unknow URN"][] = $portGroupId;
					continue;
				}

				$portName = array_slice($devicePort, 1);
				if (count($portName) == 0) {
					$devicePortArray = explode("-", $devicePort[0]);
					if(count(array_slice($devicePortArray, 1)) == 0) {
						$devicePort[0] = preg_replace('/__+/', '_', $devicePort[0]);
						$devicePortArray = explode("_", $devicePort[0]);
						if(count($devicePortArray) > 1) {
							$portName = implode("_", array_slice($devicePortArray, 1));
							$deviceName = $devicePortArray[0];
						} else {
							$portName = implode(":", $portName);
							$deviceName = $devicePort[0];
						}
					} else {
						if($devicePortArray[0] != "bi" && (strlen($devicePortArray[1]) > 1 ||
								is_numeric($devicePortArray[1]))) {
									$deviceName = $devicePortArray[0];
									$portName = implode("-", array_slice($devicePortArray, 1));
								} elseif(count($devicePortArray) > 2) {
									if (strlen($devicePortArray[count($devicePortArray)-1]) < 2){
										$deviceName = implode("-",
												array_slice($devicePortArray, 0, count($devicePortArray)-2));
										$portName = $devicePortArray[count($devicePortArray)-2].
										"-".
										$devicePortArray[count($devicePortArray)-1];
									} else {
										$deviceName = implode("-",
												array_slice($devicePortArray, 0, count($devicePortArray)-1));
										$portName = implode("-", array_slice($devicePortArray, -1));
									}
								} else {
									$portName = implode(":", $portName);
									$deviceName = $devicePort[0];
								}
					}
				} else {
					$portName = implode(":", $portName);
					$deviceName = $devicePort[0];
				}
				
				$this->addURN(
						$domainName,
						$deviceName,
						$urn,
						$portName,
						$portGroupId,
						$this->parseVlanRangeByURN($domainNode, $portGroupId, $urn));
			}
		}
	}
	
	function parseAlias($domainNode, $portGroupNode, $urn) {
		$relationNodes = $this->xpath->query(".//x:Relation", $portGroupNode);
		foreach ($relationNodes as $relationNode) {
			$portGroupNodes = $this->xpath->query(".//x:PortGroup", $relationNode);
			foreach ($portGroupNodes as $portGroupNode) {
				$this->addAlias($urn, $portGroupNode->getAttribute('id'));
			}
		}
	}
	
	function parseVlanRangeByURN($domainNode, $portGroupId, $urn) {
		$relationNodes = $this->xpath->query(".//x:Relation", $domainNode);
		foreach ($relationNodes as $relationNode) {
			$portGroupNodes = $this->xpath->query(".//x:PortGroup", $relationNode);
			if($portGroupNodes) {
				foreach ($portGroupNodes as $portGroupNode) {
					$id = $portGroupNode->getAttribute('id');

					$temp = explode(":", $id);
					if ($temp[0] !== "urn") {
						$this->errors["Unknow URN"][$id] = null;
						continue;
					}

					if ($id === $portGroupId) {
						$this->parseAlias($domainNode, $portGroupNode, $urn);
						
						$vlanRangeNode = $this->xpath->query(".//x:LabelGroup", $portGroupNode);

						if($vlanRangeNode->item(0)) {
							return $vlanRangeNode->item(0)->nodeValue;
						} else {
							continue;
						}
					}
				}
			}
		}
		return null;
	}

	function parseProviderData() {
		$nsaNodes = $this->xpath->query("//x:nsa");
		foreach ($nsaNodes as $nsaNode) {
			$idString = $nsaNode->getAttribute('id');
			$id = explode(":", $idString);
			$domainName = $id[3];
			$longitudeNode = $this->xpath->query(".//longitude", $nsaNode);
			$latitudeNode = $this->xpath->query(".//latitude", $nsaNode);
			$interfaceNodes = $this->xpath->query(".//interface", $nsaNode);
			$featureNodes = $this->xpath->query(".//feature", $nsaNode);
			$type = null;
			$connection = null;
			$discovery = null;
			$lat = null;
			$lng = null;
			
			foreach ($featureNodes as $featureNode) {
				$providerType = $featureNode->getAttribute('type');
				
				if ("vnd.ogf.nsi.cs.v2.role.uPA" == $providerType) {
					$type = "BRIDGE";
				} elseif ("vnd.ogf.nsi.cs.v2.role.aggregator" == $providerType) {
					$type = "AGGREGATOR";
				} 
			}
			
			foreach ($interfaceNodes as $interfaceNode) {
				$serviceType = $this->xpath->query(".//type", $interfaceNode);
				$serviceUrl = $this->xpath->query(".//href", $interfaceNode);
				
				if ($serviceType->item(0)) {
					if ($serviceType->item(0)->nodeValue == 
							"application/vnd.ogf.nsi.cs.v2.provider+soap") {
						$connection = $serviceUrl->item(0)->nodeValue;
						
						//Alguns dominios distribuem a topologia
						//em partes por rede. Eh meio estranho, pois
						//a topologia de um dominio esta em outro. Verificar consistencia.
					} elseif ("application/vnd.ogf.nsi.topology.v2+xml" == 
							$serviceType->item(0)->nodeValue) {
						$discovery = $serviceUrl->item(0)->nodeValue;
					} elseif ("application/vnd.ogf.nsi.dds.v1+xml" ==
							$serviceType->item(0)->nodeValue) {
						$discovery = $serviceUrl->item(0)->nodeValue;
					}
				}
			}
			
			if($longitudeNode->item(0)) {
				$lat = $latitudeNode->item(0)->nodeValue;
				$lng = $longitudeNode->item(0)->nodeValue;
			}
			
			$this->addProviderData($domainName, $idString, $type, $connection, $discovery, $lat, $lng);
		}
	}

	function endsWith($haystack, $needle) {
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}

	function removeLastChars($string, $number) {
		return substr($string,0,-$number);
	}
}

?>