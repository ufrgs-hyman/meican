<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\nsi;

/**
 * Parser de mensagens e topologias NSI.
 *
 * Suporta os documentos:
 *      NSI Topology Description 2.0 
 *      NSA Document Description 1.0
 * Suporta os seguintes protocolos:
 *      NSI Discovery Service 1.0
 *
 * @author Maurício Quatrin Guerreiro
 */
class NSIParser {

    private $topology = array();
    private $errors = array();
    private $docs = [];
    private $xpath;
    private $url;
    private $xml;
    private $error;

    function getXml() {
        return $this->xml->saveXML();
    }

    function loadXml($input) {
        try {
            $this->xml = new \DOMDocument();
            $this->xml->loadXML($input);
            $this->xpath = new \DOMXpath($this->xml);
            $this->checkEncoding();
        } catch (\Exception $e) {
            $this->error = true;
        }
    }
    
    function isDS() {
        if ($this->error) return false;
        
        $xmlns = "http://schemas.ogf.org/nsi/2014/02/discovery/types";
        $tagName = "subscriptions";
        foreach ($this->xml->getElementsByTagNameNS($xmlns, $tagName)
                as $sub) {
            return true;
        }
        
        return false;
    }
    
    function isTD() {
        if ($this->error) return false;
        $xmlns = "http://schemas.ogf.org/nml/2013/05/base#";
        $tagName = "Topology";
        foreach ($this->xml->getElementsByTagNameNS($xmlns, $tagName)
                as $sub) {
            return true;    
        }
        
        $xmlns = "http://schemas.ogf.org/nsi/2014/02/discovery/nsa";
        $tagName = "nsa";
        foreach ($this->xml->getElementsByTagNameNS($xmlns, $tagName)
                as $sub) {
            return true;
        }
        
        return false || $this->isDS();
    }
    
    function parseTopology() {
        if ($this->isDS()) {
            $this->parseDocuments();
        } elseif($this->isTD()) {
            $this->parseNetworks($this->xml);
            $this->parseProviders($this->xml);
        }
    }

    function checkEncoding() {
        $xmlns = "http://schemas.ogf.org/nsi/2014/02/discovery/types";
        $tagName = "document";
        foreach ($this->xml->getElementsByTagNameNS($xmlns, $tagName) 
                as $docNode) {
            $docTypeNode = $this->xpath->query(".//type", $docNode);
            $docContentNode = $this->xpath->query(".//content", $docNode);

            if($docContentNode->item(0)->getAttribute("contentType") == "application/x-gzip" && 
                    $docContentNode->item(0)->getAttribute('contentTransferEncoding') == "base64") {
                $contentDOM = new \DOMDocument();
                $contentDOM->loadXML(gzdecode(base64_decode($docContentNode->item(0)->nodeValue)));
                switch ($docTypeNode->item(0)->nodeValue) {
                    case "vnd.ogf.nsi.topology.v2+xml":
                        $xmlns = "http://schemas.ogf.org/nml/2013/05/base#";
                        $tagName = "Topology";
                        foreach ($contentDOM->getElementsByTagNameNS($xmlns, $tagName)
                                as $netNode) {
                            $node = $this->xml->importNode($netNode, true);
                            $docContentNode->item(0)->nodeValue = "";
                            $docContentNode->item(0)->removeAttribute("contentType");
                            $docContentNode->item(0)->removeAttribute('contentTransferEncoding');
                            $docContentNode->item(0)->appendChild($node);
                        }
                        break;
                    case "vnd.ogf.nsi.nsa.v1+xml":
                        $xmlns = "http://schemas.ogf.org/nsi/2014/02/discovery/nsa";
                        $tagName = "nsa";
                        foreach ($contentDOM->getElementsByTagNameNS($xmlns, $tagName)
                                as $nsaNode) {
                            $node = $this->xml->importNode($nsaNode, true);
                            $docContentNode->item(0)->nodeValue = "";
                            $docContentNode->item(0)->removeAttribute("contentType");
                            $docContentNode->item(0)->removeAttribute('contentTransferEncoding');
                            $docContentNode->item(0)->appendChild($node);
                        }
                    default:
                        break;
                }
            }
        }
    }

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
    
    function getData() {
        return $this->topology;
    }

    function getErrors() {
        return $this->errors;
    }
    
    function addProviderData($domainName, $nsa, $type, $name, $lat, $lng, $peerings) {
        $nsa = $nsa;
        $nsa = str_replace("urn:ogf:network:","",$nsa);
        $this->topology["domains"][$domainName]["nsa"][$nsa]['name'] = $name;
        $this->topology["domains"][$domainName]["nsa"][$nsa]["type"] = $type;
        $this->topology["domains"][$domainName]["nsa"][$nsa]["lat"] = $lat;
        $this->topology["domains"][$domainName]["nsa"][$nsa]["lng"] = $lng;
        foreach ($peerings as $peering) {
            $this->topology["domains"][$domainName]["nsa"][$nsa]["peerings"][] = str_replace("urn:ogf:network:","",$peering);
        }       
    }
    
    function addDevice($netNode, $deviceName, $lat, $lng, $address) {
        $netUrn = str_replace("urn:ogf:network:","",$netNode->getAttribute('id'));
        $id = explode(":", $netUrn);
        //         0   1     2         3        4    5
        //        urn:ogf:network:cipo.rnp.br:2014::POA
        
        $domainName = strtolower($id[0]);
        $this->topology["domains"][
            $domainName]["nets"][$netUrn]["devices"][
            $deviceName]['lat'] = $lat;
        $this->topology["domains"][
            $domainName]["nets"][$netUrn]["devices"][
            $deviceName]['lng'] = $lng;
        $this->topology["domains"][
            $domainName]["nets"][$netUrn]["devices"][
            $deviceName]['address'] = $address;
    }
    
    function addProviderService($domainName, $nsa, $service) {
        $nsa = $nsa;
        $nsa = str_replace("urn:ogf:network:","",$nsa);
        $this->topology["domains"][$domainName]["nsa"][$nsa]["services"][$service['url']] = $service['type'];
    }

    function addPort($netId, $netName, $biPortId, $biportName, $portId, $portType, 
            $vlan, $alias, $lat=null, $lng=null) {
        $netUrn = str_replace("urn:ogf:network:","",$netId);
        $netUrn = $netUrn;
        $portUrn = str_replace("urn:ogf:network:","",$portId);
        $portUrn = $portUrn;
        $biPortUrn = str_replace("urn:ogf:network:","",$biPortId);
        $biPortUrn = $biPortUrn;
        $aliasUrn = str_replace("urn:ogf:network:","",$alias);
        $aliasUrn = strtolower($aliasUrn);
        
        $id = explode(":", $netId);
        //         0   1     2         3        4    5
        //        urn:ogf:network:cipo.rnp.br:2014::POA

        $domainName = strtolower($id[3]);

        if (strpos('urn:ogf:network',$biPortId) !== false) {
            $this->errors["Unknown URN"][$biPortId] = null;
            return;
        }

        $localId = str_replace($netId.":", "", $portId);
        
        if (!isset($this->topology["domains"][
                $domainName]["nets"][$netUrn]["biports"][$biPortUrn])) {
            $this->topology["domains"][
                $domainName]["nets"][$netUrn]["name"] = $netName;
            $this->topology["domains"][
                $domainName]["nets"][$netUrn]["biports"][$biPortUrn] = array();
            $this->topology["domains"][
                $domainName]["nets"][$netUrn]["biports"][$biPortUrn]["name"] = $biportName;
        } 
        
        $this->topology["domains"][
                $domainName]["nets"][$netUrn]["biports"][
                        $biPortUrn]["uniports"][$portUrn]['name'] = $localId;
        $this->topology["domains"][
                $domainName]["nets"][$netUrn]["biports"][
                        $biPortUrn]["uniports"][$portUrn]['type'] = $portType;
        $this->topology["domains"][
                $domainName]["nets"][$netUrn]["biports"][
                        $biPortUrn]['vlan'] = $vlan;
        if ($aliasUrn) 
            $this->topology["domains"][
                $domainName]["nets"][$netUrn]["biports"][
                        $biPortUrn]["uniports"][$portUrn]['aliasUrn'] = $aliasUrn;

        if ($lat) {
            $this->topology["domains"][
                $domainName]["nets"][$netUrn]["biports"][
                        $biPortUrn]['lat'] = $lat;
            $this->topology["domains"][
                $domainName]["nets"][$netUrn]["biports"][
                        $biPortUrn]['lng'] = $lng;
        }
    }

    function parseDocuments() {
        $xmlns = "http://schemas.ogf.org/nsi/2014/02/discovery/types";
        $tagName = "document";
        foreach ($this->xml->getElementsByTagNameNS($xmlns, $tagName) 
                as $docNode) {

            if(in_array($docNode->getAttribute('id'), $this->docs)) continue;
            $this->docs[] = $docNode->getAttribute('id');
            $docTypeNode = $this->xpath->query(".//type", $docNode);
            $docContentNode = $this->xpath->query(".//content", $docNode);

            switch ($docTypeNode->item(0)->nodeValue) {
                case "vnd.ogf.nsi.topology.v2+xml":
                    $this->parseNetworks($docNode);
                    break;
                case "vnd.ogf.nsi.nsa.v1+xml":
                    $this->parseProviders($docNode);
                default:
                    break;
            }
        }
    }

    function parseNetworks($dom) {
        $xmlns = "http://schemas.ogf.org/nml/2013/05/base#";
        $tagName = "Topology";
        foreach ($dom->getElementsByTagNameNS($xmlns, $tagName) 
                as $netNode) {
            if($netNode->prefix != "") $netNode->prefix .= ":";
            $netId = $netNode->getAttribute('id');
            $netUrn = str_replace("urn:ogf:network:","",$netId);
            $netUrn = $netUrn;
            $this->xpath->registerNamespace('x', $xmlns);
            $netNameNode = $this->xpath->query(".//x:name", $netNode);
            if ($netNameNode->item(0)) {
                $netName = $netNameNode->item(0)->nodeValue;
            } else {
                $netName = null;
            }
            
            $id = explode(":", $netId);
            //         0   1     2         3        4    5
            //        urn:ogf:network:cipo.rnp.br:2014::POA
            
            $domainName = strtolower($id[3]);
            
            $longitudeNode = $this->xpath->query(".//longitude", $netNode);
            $latitudeNode = $this->xpath->query(".//latitude", $netNode);
            $addressNode = $this->xpath->query(".//address", $netNode);
            
            if($longitudeNode->item(0)) {
                $this->topology["domains"][
                        $domainName]["nets"][$netUrn]["lat"] = $latitudeNode->item(0)->nodeValue;
                $this->topology["domains"][
                        $domainName]["nets"][$netUrn]["lng"] = $longitudeNode->item(0)->nodeValue;
                $this->topology["domains"][
                        $domainName]["nets"][$netUrn]["address"] = $addressNode->item(0)->nodeValue;
            }
            
            if (!isset($this->topology["domains"][
                    $domainName]["nets"][$netUrn]["version"]) ||
                ($this->topology["domains"][
                        $domainName]["nets"][$netUrn]["version"] != $netNode->getAttribute('version'))) {

                $this->topology["domains"][
                    $domainName]["nets"][$netUrn]["name"] = $netName;
                $this->topology["domains"][
                        $domainName]["nets"][$netUrn]["version"] = $netNode->getAttribute('version');
                
                $this->parseBiPorts($netNode, $netId, $netName);
            }
        }
    }
    
    function parseSubscriptions() {
        $xmlns = "http://schemas.ogf.org/nsi/2014/02/discovery/types";
        $tagName = "subscription";
        foreach ($this->xml->getElementsByTagNameNS($xmlns, $tagName)
                as $subNode) {
            $this->topology["subs"][$subNode->getAttribute('id')] = [];
            $reqIdNode = $this->xpath->query(".//requesterId", $subNode);
            $this->topology["subs"][$subNode->getAttribute('id')]
                ['requesterId'] = $reqIdNode->item(0)->nodeValue;
            $callbackNode = $this->xpath->query(".//callback", $subNode);
            $this->topology["subs"][$subNode->getAttribute('id')]
                ['callback'] = $callbackNode->item(0)->nodeValue;
        }
    }
    
    function parseNotifications() {
        $xmlns = "http://schemas.ogf.org/nsi/2014/02/discovery/types";
        $tagName = "notifications";
        foreach ($this->xml->getElementsByTagNameNS($xmlns, $tagName)
                as $notNode) {
            $this->topology["nots"][$notNode->getAttribute('id')] 
                ['providerId'] = str_replace(
                "urn:ogf:network:","",$notNode->getAttribute('providerId'));
        }
    }
    
    function parseLocalProvider() {
        $xmlns = "http://schemas.ogf.org/nsi/2014/02/discovery/types";
        $tagName = "local";
        foreach ($this->xml->getElementsByTagNameNS($xmlns, $tagName)
                as $local) {
            $documents = $this->xpath->query(".//".
                    $local->prefix.":document", $local);
            if($documents) {
                foreach ($documents as $document) {
                    $this->topology["local"]["nsa"] = str_replace(
                            "urn:ogf:network:","",$document->getAttribute('id'));
                }
            }
        }
    }

    function parseBiPorts($netNode, $netId, $netName) {
        $biPortNodes = $this->xpath->query(".//x:BidirectionalPort", $netNode);
        if($biPortNodes) {
            foreach ($biPortNodes as $biPortNode) {
                $biPortId = $biPortNode->getAttribute('id');
                $id = explode(":", $biPortId);

                if ($id[0] !== "urn") {
                    $this->errors["Unknown URN"][$biPortId] = null;
                    continue;
                }
                
                $biportNameNode = $this->xpath->query(".//x:name", $biPortNode);
                if ($biportNameNode->item(0)) {
                    $biportName = $biportNameNode->item(0)->nodeValue;
                } else {
                    $biportName = str_replace($netId.":", "", $biPortId);
                }

                $this->parseUniPorts($netNode, $biPortNode, $netId, $netName, $biPortId, $biportName);
            }
        }
    }

    function parseUniPorts($netNode, $biPortNode, $netId, $netName, $biPortId, $biportName) {
        $portNodes = $this->xpath->query(".//x:PortGroup", $biPortNode);
        if($portNodes) {
            foreach ($portNodes as $portNode) {
                $portId = $portNode->getAttribute('id');

                $id = explode(":", $portId);
                if ($id[0] !== "urn") {
                    $this->errors["Unknown URN"][$portId] = null;
                    continue;
                }

                $vlanAndAlias = $this->parseVlanAndAlias($netNode, $portId);
                
                $this->addPort(
                        $netId,
                        $netName,
                        $biPortId,
                        $biportName,
                        $portId,
                        $this->parseUniPortType($netNode, $portId),
                        $vlanAndAlias[0],
                        $vlanAndAlias[1],
                        $vlanAndAlias[2],
                        $vlanAndAlias[3]
                );
                #$this->parseDevice($netNode, $portId));
            }
        }
    }
    
    function parseDevice($netNode, $portId) {
        $deviceNodes = $this->xpath->query(".//x:Node", $netNode);
        if($deviceNodes)
            foreach ($deviceNodes as $deviceNode) {
                $relationNodes = $this->xpath->query(".//x:Relation", $deviceNode);
                if($relationNodes)
                    foreach ($relationNodes as $relationNode) {
                        foreach ($relationNode->childNodes as $portNode) {
                            if($portId == $portNode->getAttribute('id')) {
                                $longitudeNode = $this->xpath->query(".//longitude", $deviceNode);
                                $latitudeNode = $this->xpath->query(".//latitude", $deviceNode);
                                $addressNode = $this->xpath->query(".//address", $deviceNode);
                                $nameNode = $this->xpath->query(".//x:name", $deviceNode);
                                $this->addDevice($netNode, $nameNode->item(0)->nodeValue, 
                                    $latitudeNode->item(0) ? $latitudeNode->item(0)->nodeValue : null, 
                                    $longitudeNode->item(0) ? $longitudeNode->item(0)->nodeValue : null, 
                                    $addressNode->item(0) ? urldecode($addressNode->item(0)->nodeValue) : null);
                                return $nameNode->item(0)->nodeValue;
                            }
                        }   
                    }
            }
        
        return null;
    }
    
    function parseAlias($portNode) {
        $relationNodes = $this->xpath->query(".//x:Relation", $portNode);
        foreach ($relationNodes as $relationNode) {
            $portNodes = $this->xpath->query(".//x:PortGroup", $relationNode);
            foreach ($portNodes as $portNode) {
                $portId = $portNode->getAttribute('id');
                $id = explode(":", $portId);
                if ($id[0] !== "urn") {
                    $this->errors["Unknown URN"][$portId] = null;
                    continue;
                }
                return $portId;
            }
        }
        return null;
    }
    
    function parseVlanAndAlias($netNode, $portId) {
        $relationNodes = $this->xpath->query(".//x:Relation", $netNode);
        foreach ($relationNodes as $relationNode) {
            $portNodes = $this->xpath->query(".//x:PortGroup", $relationNode);
            if($portNodes) {
                foreach ($portNodes as $portNode) {
                    $id = $portNode->getAttribute('id');

                    $temp = explode(":", $id);
                    if ($temp[0] !== "urn") {
                        $this->errors["Unknown URN"][$id] = null;
                        continue;
                    }

                    if ($id === $portId) {
                      
                        $vlanRangeNode = $this->xpath->query(".//x:LabelGroup", $portNode);
                        $locationNode = $this->xpath->query(".//x:Location", $portNode);

                        $lat = null;
                        $lng = null;
                        if ($locationNode->item(0)) {
                            # cuidado com o xpath, ele aceita o node referencia nulo e nesse
                            # caso ele procura por todo o documento.
                            $latNode = $this->xpath->query(".//x:lat", $locationNode->item(0));
                            $lat = $latNode->item(0)->nodeValue;
                            $lngNode = $this->xpath->query(".//x:long", $locationNode->item(0));
                            $lng = $lngNode->item(0)->nodeValue;
                        }

                        if($vlanRangeNode->item(0)) {
                            return [$vlanRangeNode->item(0)->nodeValue, 
                                    $this->parseAlias($portNode),
                                    $lat, $lng];
                        } else {
                            continue;
                        }
                    }
                }
            }
        }
        return null;
    }
    
    function parseUniPortType($netNode, $portId) {
        $relationNodes = $this->xpath->query(".//x:Relation", $netNode);
        foreach ($relationNodes as $relationNode) {
            $portNodes = $this->xpath->query(".//x:PortGroup", $relationNode);
            if($portNodes) {
                foreach ($portNodes as $portNode) {
                    $id = $portNode->getAttribute('id');
    
                    $temp = explode(":", $id);
                    if ($temp[0] !== "urn") {
                        $this->errors["Unknown URN"][$id] = null;
                        continue;
                    }
    
                    if ($id === $portId) {
                        if ($relationNode->getAttribute("type") == 
                                "http://schemas.ogf.org/nml/2013/05/base#hasInboundPort") {
                            return 'IN';
                        } elseif ($relationNode->getAttribute("type") == 
                                "http://schemas.ogf.org/nml/2013/05/base#hasOutboundPort") {
                            return 'OUT';
                        }
                    }
                }
            }
        }
        return null;
    }

    function parseProviders($docNode) {
        $xmlns = "http://schemas.ogf.org/nsi/2014/02/discovery/nsa";
        $tagName = "nsa";
        foreach ($docNode->getElementsByTagNameNS($xmlns, $tagName)
                as $nsaNode) {
            $idString = $nsaNode->getAttribute('id');
            $id = explode(":", $idString);
            $domainName = strtolower($id[3]);
            $nameNode = $this->xpath->query(".//name", $nsaNode);
            $longitudeNode = $this->xpath->query(".//longitude", $nsaNode);
            $latitudeNode = $this->xpath->query(".//latitude", $nsaNode);
            $interfaceNodes = $this->xpath->query(".//interface", $nsaNode);
            $featureNodes = $this->xpath->query(".//feature", $nsaNode);
            $peeringNodes = $this->xpath->query(".//peersWith", $nsaNode);
            $type = null;
            $lat = null;
            $lng = null;
            
            if ($nameNode->item(0)) {
                $name = $nameNode->item(0)->nodeValue;
            } else {
                $name = $domainName;
            }
            
            foreach ($featureNodes as $featureNode) {
                $providerType = $featureNode->getAttribute('type');
                
                if ($type != "AGG" && "vnd.ogf.nsi.cs.v2.role.uPA" == $providerType) {
                    $type = "UPA";
                } elseif ("vnd.ogf.nsi.cs.v2.role.aggregator" == $providerType) {
                    $type = "AGG";
                } 
            }
            
            foreach ($interfaceNodes as $interfaceNode) {
                $serviceType = $this->xpath->query(".//type", $interfaceNode);
                $serviceUrl = $this->xpath->query(".//href", $interfaceNode);
                
                $service = [];
                if ($serviceType->item(0)) {
                    $validService = true;
                    switch ($serviceType->item(0)->nodeValue) {
                        case "application/vnd.ogf.nsi.cs.v2.provider+soap":
                        case "application/vnd.org.ogf.nsi.cs.v2+soap":
                            $service["type"] = "NSI_CSP_2_0"; break;
                        case "application/vnd.ogf.nsi.topology.v2+xml":
                            $service["type"] = "NSI_TD_2_0"; break;                     
                        case "application/nmwg.topology+xml":
                            $service["type"] = "NMWG_TD_3_0"; break;
                        case "application/vnd.ogf.nsi.dds.v1+xml":
                            $service["type"] = "NSI_DS_1_0"; break;
                        default: 
                            $service["type"] = $serviceType->item(0)->nodeValue;
                            $this->errors["Unknown Service"][$serviceUrl->item(0)->nodeValue] = $serviceType->item(0)->nodeValue;
                            $validService = false;
                    }
                    if ($validService) {
                        $service["url"] = trim($serviceUrl->item(0)->nodeValue);
                        $this->addProviderService($domainName, $idString, $service);
                    }
                }
            }
            
            if($longitudeNode->item(0)) {
                $lat = $latitudeNode->item(0)->nodeValue;
                $lng = $longitudeNode->item(0)->nodeValue;
            }
            
            $peerings = [];
            foreach ($peeringNodes as $peeringNode) {
                $peerings[] = $peeringNode->nodeValue;
            }
            
            $this->addProviderData($domainName, $idString, $type, $name, $lat, $lng, $peerings);
        }
    }
}
    
?>