<?php

include_once 'apps/domain/models/domain_info.inc';
include_once 'apps/domain/models/network_info.inc';
include_once 'apps/domain/models/device_info.inc';
include_once 'apps/domain/models/urn_info.inc';

class Topology {

    public static function getURNTopology() {
        $domain = new domain_info();
        //$ldom = $domain->getLocalDomain();

        // atribui variáveis para chamar o comando
        $perfFile = '/opt/perfsonar_ps/topology_service/bin/client.pl';
        //$perfServer = "http://$ldom->topo_ip:8012/perfSONAR_PS/services/topology";
        $perfRequest = '/opt/perfsonar_ps/topology_service/doc/requests/TSQueryRequest-all.xml';
        $xmlFile = '/var/www/topology/topology.xml';

        // Executa comando para chamar o Perfsonar
        //system("perl $perfFile $perfServer $perfRequest > $xmlFile", $retval);
        $retval = 0;
        if ($retval == 0) {

            if (!file_exists($xmlFile)) {
                Framework::debug("Error: Topology file does not exist");
                return FALSE;
            }

            $xml = new SimpleXmlElement($xmlFile, 0, true);
            //        try {
            //            $xml = new SimpleXmlElement($xmlFile, 0, true);
            //        } catch (Exception e) {
            //
            //        }

            if (!is_a($xml, "SimpleXmlElement")) {
                Framework::debug("Error: Unable to read XML file");
                return FALSE;
            }

            // lê as URNs do arquivo de topologia, preenche o vetor $urns_top
            $nodes = $xml->children('SOAP-ENV', true)->Body->children('nmwg', true)->message->data->children('nmtopo', true)->topology->children('ns1', true)->domain->node;

            $urns_top = array();
            $top = array();
            $index = 0;
            if ($nodes)
                foreach ($nodes as $n) {
                    $ports = $n->port;

                    if ($ports)
                        foreach ($ports as $p) {
                            $remoteLink = $p->link->remoteLinkId;
                            if ($remoteLink == 'urn:ogf:network:domain=*:node=*:port=*:link=*') {
                                $urn = new stdClass();
                                $urn->id = $index++;
                                $urn->name = (string) $p->link->attributes();
                                $urn->vlan = (string) $p->link->SwitchingCapabilityDescriptors->switchingCapabilitySpecificInfo->vlanRangeAvailability;
                                $urn->max_capacity = (integer) $p->link->maximumReservableCapacity;
                                $urn->min_capacity = (integer) $p->link->minimumReservableCapacity;
                                $urn->granularity = (integer) $p->link->granularity;

                                // procura pela porta da URN
                                $searchPort = $urn->name;
                                // elimina string até achar o atributo 'port='
                                while (substr($searchPort, 0, 5) != "port=")
                                    $searchPort = substr($searchPort, 1, strlen($searchPort));

                                $port = "";
                                // atribui à porta o número da mesma
                                for ($j = 0; $searchPort[$j + 5] != ':'; $j++)
                                    $port .= $searchPort[$j + 5];

                                $urn->port = $port;

                                $top[$urn->id] = $urn->name; // vetor para comparação com o banco
                                $urns_top[] = $urn;
                            }
                        }
                }

            // lê as URNs cadastradas no banco de dados do sistema, preenche o vetor $urns_db
            $urn = new urn_info();
            $allUrns = $urn->fetch(FALSE);

            $db = array();
            if ($allUrns)
                foreach ($allUrns as $u) {
                    $db[$u->urn_id] = $u->urn_string; // vetor para comparação com a topologia
                }

            // calcula diferença entre os vetores da topologia e do banco, retorna apenas os novos
            $urn_diff = array_diff($top, $db); // urn_diff = top - db
            // calcula diferença entre os vetores do banco e da topologia, retorna os inválidos
            //$invalid = array_diff($db, $top);
            //Framework::debug("urn invalid",$invalid);
            // preenche vetor a ser retornado
            $urns = array();
            foreach ($urn_diff as $ind => $d) {
                $urns[] = $urns_top[$ind];
            }

            return ($urns);
        } else {
            Framework::debug("Error: failed to connect to PerfSONAR");
            return FALSE;
        }
    }

    /**
     * @return <array> StdClass: returns the networks of the system along with the cointaining devices
     * Example:
     * networks[ind]->id = <net_id>
     * networks[ind]->name = <net_descr>
     * networks[ind]->devices = <array> devices :
     *              devices[ind]->id = <dev_id>
     *              devices[ind]->name = <dev_descr>
     *
     */
    public static function getNetworks() {
        $net_info = new network_info();
        $allNets = $net_info->fetch(FALSE);

        $networks = array();

        if ($allNets)
            foreach ($allNets as $net) {
                $network = new stdClass();
                $network->id = $net->net_id;
                $network->name = $net->net_descr;

                $dev_info = new device_info();
                $dev_info->net_id = $net->net_id;
                $allDevs = $dev_info->fetch(FALSE);

                $devices = array();

                if ($allDevs)
                    foreach ($allDevs as $dev) {
                        $device = new stdClass();
                        $device->id = $dev->dev_id;
                        $device->name = $dev->dev_descr;

                        $devices[] = $device;
                    }

                $network->devices = $devices;

                $networks[] = $network;
            }

        return $networks;
    }

    // *** OBSOLET ***
    // função substituída pela getURNsInfo
//    public static function callGetURNDetails($dom_id, $flowArray) {
//        /**
//         * $flowArray: stdClass ( flow->id, flow->src_urn_string, flow->dst_urn_string )
//         *
//         */
//
//        $urns = array();
//        foreach ($flowArray as $f) {
//
//            unset($urnList);
//
//            $urnList->flow_id = $f->id;
//
//            if ($f->src_urn) {
//                $urn = new urn_info();
//                $urn->urn_id = $f->src_urn;
//                $res = $urn->fetch(FALSE);
//
//                $net = new network_info();
//                $net->net_id = $res[0]->network_id;
//                $res_net = $net->fetch(FALSE);
//                $urnList->source->network_id = $res_net[0]->net_id;
//                $urnList->source->network = $res_net[0]->net_descr;
//
//                $dev = new device_info();
//                $dev->dev_id = $res[0]->device_id;
//                $res_dev = $dev->fetch(FALSE);
//                $urnList->source->device_id = $res_dev[0]->dev_id;
//                $urnList->source->device = $res_dev[0]->dev_descr;
//
//                $urnList->source->port = $res[0]->port;
//                $urnList->source->urn = $res[0]->urn_string;
//            }
//            if ($f->dst_urn) {
//                $urn = new urn_info();
//                $urn->urn_id = $f->dst_urn;
//                $res = $urn->fetch(FALSE);
//
//                $net = new network_info();
//                $net->net_id = $res[0]->network_id;
//                $res_net = $net->fetch(FALSE);
//                $urnList->dest->network_id = $res_net[0]->net_id;
//                $urnList->dest->network = $res_net[0]->net_descr;
//
//                $dev = new device_info();
//                $dev->dev_id = $res[0]->device_id;
//                $res_dev = $dev->fetch(FALSE);
//                $urnList->dest->device_id = $res_dev[0]->dev_id;
//                $urnList->dest->device = $res_dev[0]->dev_descr;
//
//                $urnList->dest->port = $res[0]->port;
//                $urnList->dest->urn = $res[0]->urn_string;
//            }
//
//            $urns[] = $urnList;
//        }
//
//        return $urns;
//    }

    // *** OBSOLET ***
    // função substituída pela getURNDetails
//    public static function getURN ($dom_id, $device, $port) {
//        $urn = new urn_info();
//        $urn->device_id = $device;
//        $urn->port = $port;
//
//        $res = $urn->fetch(FALSE);
//
//        unset($info);
//        $info->urn = $res[0]->urn_string;
//        $info->vlan = $res[0]->vlan;
//        $info->max_capacity = $res[0]->max_capacity;
//        $info->min_capacity = $res[0]->min_capacity;
//        $info->granularity = $res[0]->granularity;
//
//        return $info;
//    }

    static public function getURNsInfo($urn_string_array) {
        $urns = array();
        $ind = 0;

        if ($urn_string_array)
            foreach ($urn_string_array as $urn_str) {
                if ($urn_str) {
                    $urn_info = new urn_info();
                    $urn_info->urn_string = $urn_str;
                    $urn = $urn_info->fetch(FALSE);

                    if (!$urn) {
                        $urns[$ind] = NULL;
                        $ind++;
                        continue;
                    }

                    $net_info = new network_info();
                    $net_info->net_id = $urn[0]->net_id;
                    $network = $net_info->fetch(FALSE);

                    $dev_info = new device_info();
                    $dev_info->dev_id = $urn[0]->dev_id;
                    $device = $dev_info->fetch(FALSE);

                    if ($network && $device) {
                        $urns[$ind]['network'] = $network[0]->net_descr;
                        $urns[$ind]['device'] = $device[0]->dev_descr;
                        $urns[$ind]['port'] = $urn[0]->port;
                    } else
                        $urns[$ind] = NULL;
                } else {
                    $urns[$ind] = NULL;
                }
                $ind++;
            }

        return $urns;
    }

    /**
     * utilizada para preencher um novo flow, busca direto na tabela urn_info
     * @return <type>
     */
    static public function getURNDetails($urn_string = NULL) {

        // lê todas as URNs do banco
        $urn_info = new urn_info();

        if ($urn_string)
            $urn_info->urn_string = $urn_string;

        $allUrns = $urn_info->fetch(FALSE);

        $networks = array();
        if ($allUrns)
            foreach ($allUrns as $u) {

                $net_info = new network_info();
                $net_info->net_id = $u->net_id;
                $res = $net_info->fetch(FALSE);

                $network = new stdClass();
                $network->id = $u->net_id;
                $network->name = $res[0]->net_descr;
                $network->latitude = $res[0]->net_lat;
                $network->longitude = $res[0]->net_lng;

                $dev_info = new device_info();
                $dev_info->dev_id = $u->dev_id;
                $res = $dev_info->fetch(FALSE);

                $device = new stdClass();
                $device->id = $u->dev_id;
                $device->name = $res[0]->dev_descr;
                $device->latitude = $res[0]->dev_lat;
                $device->longitude = $res[0]->dev_lng;

                $port = new stdClass();
                $port->urn_string = $u->urn_string;
                $port->urn_id = $u->urn_id;
                $port->vlan = $u->vlan;
                $port->port_number = $u->port;
                $port->max_capacity = $u->max_capacity;
                $port->min_capacity = $u->min_capacity;
                $port->granularity = $u->granularity;

                $device->ports = array();
                $device->ports[] = (array) $port;

                $network->devices = array();
                $network->devices[] = (array) $device;

                /**
                 * Lógica:
                 * se rede/dispositivo já está no vetor, apenas insere no final, para não inserir duplicado
                 * senão, insere todo o objeto no vetor
                 */
                $netInArray = FALSE;
                foreach ($networks as $ind => $n) {
                    if ($n['id'] == $network->id) {

                        // já existe a rede no vetor
                        $netInArray = TRUE;

                        $devInArray = FALSE;
                        foreach ($n['devices'] as $ind2 => $d) {
                            if ($d['id'] == $device->id) {
                                // já existe o dispositivo dessa rede no vetor
                                $devInArray = TRUE;
                                // insere apenas a porta do dispositivo na última posição do vetor,
                                $port = (array) $port;
                                array_push($networks[$ind]['devices'][$ind2]['ports'], $port);
                                break;
                            }
                        }

                        // se o dispositivo não está no vetor dentro da rede, insere todo o objeto 'device'
                        if (!$devInArray) {
                            $device = (array) $device;
                            array_push($networks[$ind]['devices'], $device);
                        }

                        break;
                    }
                }

                // se a rede não está no vetor, insere todo o objeto
                if (!$netInArray) {
                    $network = (array) $network;
                    array_push($networks, $network);
                }

            }

        //Framework::debug("net",$networks);
        return $networks;

    }
}

?>
