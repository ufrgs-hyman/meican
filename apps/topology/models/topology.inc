<?php

include_once 'apps/topology/models/domain_info.inc';
include_once 'apps/topology/models/network_info.inc';
include_once 'apps/topology/models/device_info.inc';
include_once 'apps/topology/models/urn_info.inc';

include_once 'apps/circuits/models/oscars_reservation.inc';

class MeicanTopology {
    
    public static function getURNTopology($dom_id) {

        if (!$dom_id)
            return FALSE;

        $domain_info = new domain_info();
        $domain_info->dom_id = $dom_id;

        if ($domain = $domain_info->fetch()) {
            $dom = $domain[0];

            $os = new OSCARSReservation();
            $os->setOscarsUrl($dom->oscars_ip);

            if ($os->getUrns()) {

                $urn_db = MeicanTopology::getURNs($dom_id);
                $db = array();
                if ($urn_db)
                    foreach ($urn_db as $u) {
                        $db[] = $u->urn_string; // vetor para comparação com a topologia
                    }

                $index = 0;
                $urns_to_return = array();
                foreach ($os->urns as $u) {

                    if (array_search($u->id, $db) === FALSE) {

                        $new_urn = new stdClass();
                        $new_urn->id = $index++;
                        $new_urn->name = $u->id;

                        $new_urn->vlan = $u->vlanRange;
                        $new_urn->max_capacity = $u->maximumReservable;
                        $new_urn->min_capacity = $u->minimumReservable;
                        $new_urn->granularity = $u->granularity;

                        // pega todo string com ID da porta e separa pelos ":"
                        $topo_attr_array = explode(":", $u->id);

                        // procura pelo ID do nodo
                        $topo_attr = explode("=", $topo_attr_array[4]);
                        $new_urn->node_id = NULL;
                        if ($topo_attr[0] == "node")
                            $new_urn->node_id = $topo_attr[1];

                        // procura pela porta
                        $topo_attr = explode("=", $topo_attr_array[5]);
                        $new_urn->port = NULL;
                        if ($topo_attr[0] == "port")
                            $new_urn->port = $topo_attr[1];


                        $urns_to_return[] = $new_urn;
                    }
                }

                return $urns_to_return;
            } else
                return FALSE;
        } else
            return FALSE;
    }
    
    public static function getURNs($dom_id, $urn_string=NULL) {
        
        $urns = array();
        
        if ($urn_string) {
            $urn_info = new urn_info();
            $urn_info->urn_string = $urn_string;
            $urn_res = $urn_info->fetch();

            $urn = new stdClass();
            $urn->urn_id = $urn_res[0]->urn_id;
            $urn->urn_string = $urn_res[0]->urn_string;

            $net = new network_info();
            $net->net_id = $urn_res[0]->net_id;
            $res = $net->fetch(FALSE);
            $urn->net_id = $res[0]->net_id;
            $urn->network = $res[0]->net_descr;

            $dev = new device_info();
            $dev->dev_id = $urn_res[0]->dev_id;
            $res = $dev->fetch(FALSE);
            $urn->dev_id = $res[0]->dev_id;
            $urn->device = $res[0]->dev_descr;

            $urn->port = $urn_res[0]->port;
            $urn->vlan = $urn_res[0]->vlan;
            $urn->max_capacity = $urn_res[0]->max_capacity;
            $urn->min_capacity = $urn_res[0]->min_capacity;
            $urn->granularity = $urn_res[0]->granularity;

            $urns[] = $urn;
            
            return $urns;
        }
        
        $aco = new Acos($dom_id, "domain_info");

        if ($aco_dom = $aco->fetch(FALSE)) {
            $children = $aco_dom[0]->findChildren();
        }

        foreach ($children as $child) {

            if ($child->model == "urn_info") {
                
                $urn_info = new urn_info();
                $urn_info->urn_id = $child->obj_id;
                $urn_res = $urn_info->fetch();
                
                $urn = new stdClass();
                $urn->urn_id = $urn_res[0]->urn_id;
                $urn->urn_string = $urn_res[0]->urn_string;

                $net = new network_info();
                $net->net_id = $urn_res[0]->net_id;
                $res = $net->fetch(FALSE);
                $urn->net_id = $res[0]->net_id;
                $urn->network = $res[0]->net_descr;

                $dev = new device_info();
                $dev->dev_id = $urn_res[0]->dev_id;
                $res = $dev->fetch(FALSE);
                $urn->dev_id = $res[0]->dev_id;
                $urn->device = $res[0]->dev_descr;

                $urn->port = $urn_res[0]->port;
                $urn->vlan = $urn_res[0]->vlan;
                $urn->max_capacity = $urn_res[0]->max_capacity;
                $urn->min_capacity = $urn_res[0]->min_capacity;
                $urn->granularity = $urn_res[0]->granularity;
                
                $urns[] = $urn;
            }
        }
        
        return $urns;
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
    public static function getNetworks($dom_id) {
        $aco = new Acos($dom_id, "domain_info");

        if ($aco_dom = $aco->fetch(FALSE)) {
            $children = $aco_dom[0]->findChildren();
        }

        $networks = array();

        foreach ($children as $child) {

//        $net_info = new network_info();
//        $allNets = $net_info->fetch(FALSE);

            if ($child->model == "network_info") {

                $net_info = new network_info();
                $net_info->net_id = $child->obj_id;
                $net = $net_info->fetch();

                $network = new stdClass();
                $network->id = $net[0]->net_id;
                $network->name = $net[0]->net_descr;

                $dev_info = new device_info();
                $dev_info->net_id = $net[0]->net_id;
                $allDevs = $dev_info->fetch(FALSE);

                $devices = array();

                if ($allDevs)
                    foreach ($allDevs as $dev) {
                        $device = new stdClass();
                        $device->id = $dev->dev_id;
                        $device->name = $dev->dev_descr;
                        $device->node_id = $dev->node_id;

                        $devices[] = $device;
                    }

                $network->devices = $devices;

                $networks[] = $network;
            }
        }

        return $networks;
    }

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
    static public function getURNDetails($dom_id, $urn_string = NULL) {

        // lê todas as URNs do banco
//        $urn_info = new urn_info();
//
//        if ($urn_string)
//            $urn_info->urn_string = $urn_string;
//
//        $allUrns = $urn_info->fetch(FALSE);
        
        $allUrns = MeicanTopology::getURNs($dom_id, $urn_string);

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
                $device->topology_node_id = $res[0]->node_id;
                $device->model = $res[0]->model;

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
