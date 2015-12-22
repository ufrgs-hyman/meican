<?php
//ESTRUTURA DA TOPOLOGIA

namespace meican\modules\topology\models;

use meican\components\PerfsonarSoapClient;

class NMWGParser {
    
    private $topology = array();
    private $errors = array();
    private $xpath;
    private $url;
    private $xml;
    private $error;
    
    function loadFile($url) {
        $this->url = $url;
        $ch = curl_init();

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,

            CURLOPT_USERAGENT => 'Meican',
            CURLOPT_URL => $this->url,
        );

        curl_setopt_array($ch , $options);

        $output = curl_exec($ch);
        curl_close($ch);
        $this->loadXml($output);
    }

    function loadXml($input) {
        try {
            $this->xml = new \DOMDocument();
            $this->xml->loadXML($input);
            $this->xpath = new \DOMXpath($this->xml);
        } catch (\Exception $e) {
            $this->error = true;
        }
    }
    
    function parseTopology() {
        $this->xpath->registerNamespace('x', "http://ogf.org/schema/network/topology/ctrlPlane/20080828/");
    
        $this->parseDomains();
    }
    
    function isTD() {
        if ($this->error) return false;
        $xmlns = "http://ogf.org/schema/network/topology/ctrlPlane/20080828/";
        $tagName = "topology";
        foreach ($this->xml->getElementsByTagNameNS($xmlns, $tagName)
                as $topology) {
            return true;
        }

        return false;
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

    function addBiPort($domainName, $deviceName, $urn,  $portName, $capMax, $capMin, $granu,
            $vlanRange, $aliasUrn) {
        $urn = str_replace("urn:ogf:network:","",$urn);
        $this->topology['domains'][$domainName]["devices"][$deviceName]["biports"][$urn] = array();
        $this->topology['domains'][$domainName]["devices"][$deviceName]["biports"][$urn]["port"] = $portName;
        $this->topology['domains'][$domainName]["devices"][$deviceName]["biports"][$urn]["capMax"] = intval($capMax)/1000000;
        $this->topology['domains'][$domainName]["devices"][$deviceName]["biports"][$urn]["capMin"] = intval($capMin)/1000000;
        $this->topology['domains'][$domainName]["devices"][$deviceName]["biports"][$urn]["granu"] = intval($granu)/1000000;
        $this->topology['domains'][$domainName]["devices"][$deviceName]["biports"][$urn]["vlan"] = $vlanRange;
        if ($aliasUrn)
        $aliasUrn = str_replace("urn:ogf:network:","",$aliasUrn);
        $this->topology['domains'][$domainName]["devices"][$deviceName]["biports"][$urn]["aliasUrn"] = $aliasUrn;
    }

    function addLocation($domainName, $deviceName, $lat, $lng, $address) {
        $this->topology['domains'][$domainName]["devices"][$deviceName]["lat"] = $lat;
        $this->topology['domains'][$domainName]["devices"][$deviceName]["lng"] = $lng;
        $this->topology['domains'][$domainName]["devices"][$deviceName]["address"] = $address;
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
                
                $locationNodes = $this->xpath->query(".//x:location", $deviceNode);
                foreach ($locationNodes as $locationNode) {
                    $value = $this->xpath->query(".//x:latitude", $locationNode);
                    $lat = $value->item(0) ? $value->item(0)->nodeValue : null;
                    $value = $this->xpath->query(".//x:longitude", $locationNode);
                    $lng = $value->item(0) ? $value->item(0)->nodeValue : null;
                    $value = $this->xpath->query(".//x:address", $locationNode);
                    $address = $value->item(0) ? $value->item(0)->nodeValue : null;
                    
                    $this->addLocation($domainName, $deviceName, $lat, $lng, $address);
                }

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
                $urn = explode(":", $urn);
                $urn[count($urn) - 1] = "";
                $urn = implode(":", $urn);
                $urn = substr($urn, 0, -1);

                $value = $this->xpath->query(".//x:remoteLinkId", $linkNode);
                $aliasUrn = $value->item(0) ? $value->item(0)->nodeValue : null;
                if ($aliasUrn) {
                    $aliasUrn = explode(":", $aliasUrn);
                    if ($aliasUrn[3] != "domain=*") {
                        $aliasUrn[count($aliasUrn) - 1] = "";
                        $aliasUrn = implode(":", $aliasUrn);
                        $aliasUrn = substr($aliasUrn, 0, -1);
                    } else {
                        $aliasUrn = null;
                    }
                }

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

                $this->addBiPort(
                        $domainName,
                        $deviceName,
                        $urn,
                        $portName,
                        $capMax,
                        $capMin,
                        $granu,
                        $vlanRange,
                        $aliasUrn);
            }
        }
    }
}

?>