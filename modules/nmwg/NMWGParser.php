<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\nmwg;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
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
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT => 'Meican',
            CURLOPT_URL => $this->url,
        );

        curl_setopt_array($ch , $options);

        $output = curl_exec($ch);
        curl_close($ch);

        if($output != null) {
            //  echo $output;
            $this->loadXml($output);
            return true;
        } else return false;
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
        $tagName = "*";
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

    function addBiPort($domainName, $locationName, $urn,  $portName, $cap, $capMax, $capMin, $granu,
            $vlanRange, $aliasUrn, $lat, $lng) {
        $urn = str_replace("urn:ogf:network:","",$urn);
        $this->topology["domains"][$domainName]["biports"][$urn] = array();
        $this->topology["domains"][$domainName]["biports"][$urn]["port"] = $portName;
        $this->topology["domains"][$domainName]["biports"][$urn]["cap"] = substr($cap, 0, -6);
        $this->topology["domains"][$domainName]["biports"][$urn]["capMax"] = substr($capMax, 0, -6);
        $this->topology["domains"][$domainName]["biports"][$urn]["capMin"] = intval($capMin)/1000000;
        $this->topology["domains"][$domainName]["biports"][$urn]["granu"] = substr($granu, 0, -6);
        $this->topology["domains"][$domainName]["biports"][$urn]["vlan"] = $vlanRange;
        $this->topology["domains"][$domainName]["biports"][$urn]['locationName'] = $locationName;
        $this->topology["domains"][$domainName]["biports"][$urn]["lat"] = $lat;
        $this->topology["domains"][$domainName]["biports"][$urn]["lng"] = $lng;

        if ($aliasUrn)
            $aliasUrn = str_replace("urn:ogf:network:","",$aliasUrn);
        
        $this->topology["domains"][$domainName]["biports"][$urn]["aliasUrn"] = $aliasUrn;
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
                    
                $this->parseLocation($domainNode, $domainName);
            }
        }
    }

    function parseLocation($domainNode, $domainName) {
        $LocationNodes = $this->xpath->query(".//x:node", $domainNode);
        if($LocationNodes) {
            foreach ($LocationNodes as $locationNode) {
                $idString = $locationNode->getAttribute('id');
                $id = explode("=", $idString);
                $locationName = $id[count($id)-1];
                
                $lat = null;
                $lng = null;
                $locationCoordNodes = $this->xpath->query(".//x:location", $locationNode);
                if($locationCoordNodes) {
                    foreach ($locationCoordNodes as $locationNode) {
                        $value = $this->xpath->query(".//x:latitude", $locationNode);
                        $lat = $value->item(0) ? $value->item(0)->nodeValue : null;
                        $value = $this->xpath->query(".//x:longitude", $locationNode);
                        $lng = $value->item(0) ? $value->item(0)->nodeValue : null;
                        $value = $this->xpath->query(".//x:address", $locationNode);
                        $address = $value->item(0) ? $value->item(0)->nodeValue : null;
                        
                    }
                }

                $this->parsePorts($domainName, $locationNode, $locationName, $lat, $lng);
            }
        }
    }

    function parsePorts($domainName, $locationNode, $locationName, $lat, $lng) {
        $portNodes = $this->xpath->query(".//x:port", $locationNode);
        if($portNodes) {
            foreach ($portNodes as $portNode) {
                $idString = $portNode->getAttribute('id');
                $id = explode("=", $idString);

                $portName = $id[count($id)-1];

                $value = $this->xpath->query(".//x:capacity", $portNode);
                $cap = $value->item(0) ? $value->item(0)->nodeValue : null;
                $value = $this->xpath->query(".//x:maximumReservableCapacity", $portNode);
                $capMax = $value->item(0) ? $value->item(0)->nodeValue : null;
                $value = $this->xpath->query(".//x:minimumReservableCapacity", $portNode);
                $capMin = $value->item(0) ? $value->item(0)->nodeValue : null;
                $value = $this->xpath->query(".//x:granularity", $portNode);
                $granu = $value->item(0) ? $value->item(0)->nodeValue : null;

                $this->parseLinks($domainName, $locationName, $portNode, $portName,
                        $cap, $capMax, $capMin, $granu, $lat, $lng);
            }
        }
    }

    function parseLinks($domainName, $locationName, $portNode, $portName, $cap, $capMax, $capMin,
            $granu, $lat, $lng) {

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

                if (!$cap) {
                    $value = $this->xpath->query(".//x:capacity", $linkNode);
                    $cap = $value->item(0) ? $value->item(0)->nodeValue : null;
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
                        $locationName,
                        $urn,
                        $portName,
                        $cap,
                        $capMax,
                        $capMin,
                        $granu,
                        $vlanRange,
                        $aliasUrn,
                        $lat, 
                        $lng);
            }
        }
    }
}

?>