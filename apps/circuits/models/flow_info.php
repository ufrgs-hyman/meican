<?php

include_once 'libs/resource_model.php';
include_once 'apps/topology/models/topology.php';

class flow_info extends Resource_Model {
    var $displayField = "path";

    public function flow_info() {
        $this->setTableName("flow_info");

        // Add all table attributes
        $this->addAttribute("flw_id", "INTEGER", true, false, false);

        $this->addAttribute("src_meican_id", "INTEGER");
        $this->addAttribute("src_urn_string", "VARCHAR");
        $this->addAttribute("src_vlan", "VARCHAR");

        $this->addAttribute("dst_meican_id", "INTEGER");
        $this->addAttribute("dst_urn_string", "VARCHAR");
        $this->addAttribute("dst_vlan", "VARCHAR");

        $this->addAttribute("path", "VARCHAR");
    }

//    public function insert() {
//        $result = parent::insert();
//
//        if ($result) {
//            $flow = $result[0];
//
//            $aco = new Acos();
//            $aco->model = $flow->getTableName();
//            $aco->obj_id = $flow->flow_id;
//
//            //arvore de acos, embaixo do usuário
//            if ($aco->addChild(AuthSystem::getUserId(),'user_info'))
//                return $flow;
//            else
//                $flow->delete();
//        }
//
//        return FALSE;
//    }]

    public function getFlowDetails2() {
        if (!isset($this->flw_id)) {
            return FALSE;
        } else {

            if ($result = $this->fetch()) {
                $flow = $result[0];

                $src_domain = new domain_info();
                $src_domain->dom_id = $flow->src_dom;
                $result2 = $src_domain->fetch(FALSE);

                $return = new stdClass();

                if ($result2) {
                    $return->src_dom_id = $flow->src_dom;
                    $return->src_dom_ip = $result2[0]->dom_ip;
                    $return->src_urn_string = $flow->src_urn_string;
                }

                $dst_domain = new domain_info();
                $dst_domain->dom_id = $flow->dst_dom;
                $result3 = $dst_domain->fetch(FALSE);

                if ($result3) {
                    $return->dst_dom_id = $flow->dst_dom;
                    $return->dst_dom_ip = $result3[0]->dom_ip;
                    $return->dst_urn_string = $flow->dst_urn_string;
                }

                return $return;

            } else return FALSE;
        }
    }

    public function getFlowDetails() {
        if (!isset ($this->flw_id)) {
            return FALSE;
        } else {

            $ret = $this->fetch(FALSE);            
            if (!$ret) {
                return FALSE;
            }
            $flow = $ret[0];
            
            

//            $domain = new domain_info();
//            $domain->dom_id = $flow->src_dom;
//            $dom = $domain->fetch(FALSE);

//            //chamada de web service para buscar os dados completos da URN
//            $endpoint = "http://{$dom[0]->dom_ip}/".Framework::$systemDirName."topology/ws";
//            $ws = new nusoap_client($endpoint,array('cache_wsdl' => 0));
//            $urnDetailsRet = $ws->call('getURNDetails', array($flow->src_urn_string));

            $urnDetailsRet = MeicanTopology::getURNDetails(NULL, $flow->src_urn_string);
            $flowData = new stdClass();

            $flowData->id = $flow->flw_id;
            $flowData->path = $flow->path;

//            $flowData->source->domain = $dom[0]->dom_descr;
//            $flowData->source->dom_id = $dom[0]->dom_id;
//            $flowData->source->oscars_ip = $dom[0]->oscars_ip;
            $flowData->source->vlan = $flow->src_vlan;
            $flowData->source->urn = $flow->src_urn_string;

            if ($urnDetailsRet) {
                $flowData->source->net_id = $urnDetailsRet[0]['id'];
                $flowData->source->network = $urnDetailsRet[0]['name'];
                $flowData->source->latitude = $urnDetailsRet[0]['latitude'];
                $flowData->source->longitude = $urnDetailsRet[0]['longitude'];
                $flowData->source->dev_id = $urnDetailsRet[0]['devices'][0]['id'];
                $flowData->source->device = $urnDetailsRet[0]['devices'][0]['name'];
                $flowData->source->port = $urnDetailsRet[0]['devices'][0]['ports'][0]['port_number'];
            } else
                return FALSE;

//            $domain = new domain_info();
//            $domain->dom_id = $flow->dst_dom;
//            $dom = $domain->fetch(FALSE);

            //chamada de web service para buscar os dados completos da URN
//            $endpoint = "http://{$dom[0]->dom_ip}/".Framework::$systemDirName."topology/ws";
//            $ws = new nusoap_client($endpoint,array('cache_wsdl' => 0));
//            $urnDetailsRet = $ws->call('getURNDetails', array($flow->dst_urn_string));

            $urnDetailsRet = MeicanTopology::getURNDetails(NULL, $flow->dst_urn_string);
//            $flowData->dest->domain = $dom[0]->dom_descr;
//            $flowData->dest->dom_id = $dom[0]->dom_id;
//            $flowData->dest->oscars_ip = $dom[0]->oscars_ip;
            $flowData->dest->vlan = $flow->dst_vlan;
            $flowData->dest->urn = $flow->dst_urn_string;

            if ($urnDetailsRet) {
                $flowData->dest->net_id = $urnDetailsRet[0]['id'];
                $flowData->dest->network = $urnDetailsRet[0]['name'];
                $flowData->dest->latitude = $urnDetailsRet[0]['latitude'];
                $flowData->dest->longitude = $urnDetailsRet[0]['longitude'];
                $flowData->dest->dev_id = $urnDetailsRet[0]['devices'][0]['id'];
                $flowData->dest->device = $urnDetailsRet[0]['devices'][0]['name'];
                $flowData->dest->port = $urnDetailsRet[0]['devices'][0]['ports'][0]['port_number'];
            } else
                return FALSE;

            return $flowData;
        }
    }

}

?>
