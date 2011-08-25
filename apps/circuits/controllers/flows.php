<?php

defined('__FRAMEWORK') or die("Invalid access.");

include_once 'libs/controller.php';

include_once 'apps/circuits/models/flow_info.inc';
include_once 'apps/circuits/controllers/reservations.php';

include_once 'apps/topology/models/domain_info.inc';
include_once 'apps/topology/models/topology.inc';
include_once 'apps/topology/models/meican_info.inc';

require_once 'includes/nuSOAP/lib/nusoap.php';
include_once 'apps/circuits/models/oscars_reservation.inc';

class flows extends Controller {

    public function flows() {
        $this->app = 'circuits';
        $this->controller = 'flows';
        $this->defaultAction = 'show';
    }

//    public function show() {
//        // destrói variáveis, caso clicou em flows antes de passar por reservations
//        Common::destroySessionVariable('res_name');
//        Common::destroySessionVariable('sel_flow');
//        Common::destroySessionVariable('sel_timer');
//        Common::destroySessionVariable('res_wizard');
//
//        $flow_info = new flow_info();
//        $allFlows = $flow_info->fetch();
//
//        if ($allFlows) {
//
//            $domains = array();
//            foreach ($allFlows as $f) {
//                if (array_search($f->src_dom, $domains) === FALSE)
//                    $domains[] = $f->src_dom;
//                if (array_search($f->dst_dom, $domains) === FALSE)
//                    $domains[] = $f->dst_dom;
//            }
//
//            $urn_string_array = array();
//
//            foreach ($domains as $d) {
//                $ind = 0;
//                $urn_string_array[$d] = array();
//                foreach ($allFlows as $f) {
//                    $urn_string_array[$d][$ind] = ($d == $f->src_dom) ? $f->src_urn_string : NULL;
//                    $ind++;
//                    $urn_string_array[$d][$ind] = ($d == $f->dst_dom) ? $f->dst_urn_string : NULL;
//                    $ind++;
//                }
//            }
//
//            $urnData = array();
//            foreach ($urn_string_array as $dom_id => $urn_array) {
//                $domain = new domain_info();
//                $domain->dom_id = $dom_id;
//                $dom = $domain->fetch(FALSE);
//                $endpoint = "http://{$dom[0]->dom_ip}/".Framework::$systemDirName."/main.php?app=topology&services&wsdl";
//
//                if ($ws = new nusoap_client($endpoint, array('cache_wsdl' => 0))) {
//                    if ($temp = $ws->call('getURNsInfo', array('urn_string_list' => $urn_array))) {
//                            $urnData[] = $temp;
//                            continue;
//                    }
//                }
//                $urnData[] = NULL;
//            }
//
//
//            $urnInfoMerge = array();
//            foreach ($urnData as $uD) {
//                if ($uD) {
//                foreach ($uD as $ind => $urn_str) {
//                    if ($urn_str)
//                        $urnInfoMerge[$ind] = $urn_str;
//                    elseif (!isset($urnInfoMerge[$ind]))
//                        $urnInfoMerge[$ind] = NULL;
//                }
//                }
//            }
//
//            //Framework::debug("urn data",$urnInfoMerge);
//
//            $flows = array();
//            $ind = 0;
//
//            foreach ($allFlows as $f) {
//                $flow = new stdClass();
//                $flow->id = $f->flw_id;
//                $flow->name = $f->flw_name;
//                $flow->bandwidth = $f->bandwidth;
//
//                $domain = new domain_info();
//                $domain->dom_id = $f->src_dom;
//                $res_dom = $domain->fetch(FALSE);
//
//                $flow->source->domain = $res_dom[0]->dom_descr;
//                $flow->source->vlan = $f->src_vlan;
//
//                if ($urnInfoMerge[$ind]) {
//                    $flow->source->network = $urnInfoMerge[$ind]['net_descr'];
//                    $flow->source->device = $urnInfoMerge[$ind]['dev_descr'];
//                    $flow->source->port = $urnInfoMerge[$ind]['port_number'];
//                } else
//                    $flow->source->urn_string = $f->src_urn_string;
//
//                $ind++;
//
//                $domain = new domain_info();
//                $domain->dom_id = $f->dst_dom;
//                $res_dom = $domain->fetch(FALSE);
//
//                $flow->dest->domain = $res_dom[0]->dom_descr;
//                $flow->dest->vlan = $f->dst_vlan;
//
//                if ($urnInfoMerge[$ind]) {
//                    $flow->dest->network = $urnInfoMerge[$ind]['net_descr'];
//                    $flow->dest->device = $urnInfoMerge[$ind]['dev_descr'];
//                    $flow->dest->port = $urnInfoMerge[$ind]['port_number'];
//                } else
//                    $flow->dest->urn_string = $f->dst_urn_string;
//
//                $ind++;
//
//                $flow->editable = TRUE;
//                $flow->deletable = TRUE;
//                $flow->selectable = FALSE;
//
//                $flows[] = $flow;
//            }
//            $this->setAction('show');
//
//            $this->setArgsToBody($flows);
//        } else {
//            $this->setAction('empty');
//
//            $args = new stdClass();
//            $args->title = _("Flows");
//            $args->message = _("You have no flow, click the button below to create a new one");
//            $args->link = array('app' => 'circuits', 'controller' => 'flows', 'action' => 'add_options');
//            $this->setArgsToBody($args);
//        }
//
//        $this->render();
//    }

    public function add_form() {
        $min = 100;
        $max = 1000;
        $div = 100;

        $this->setAction("add");

        $argsToScript = array(
            "band_min" => $min,
            "band_max" => $max,
            "band_div" => $div,
            "flash_nameReq" => _("A name is required"),
            "flash_bandInv" => _("Invalid value for bandwidth"),
            "flash_sourceReq" => _("A source is required"),
            "flash_srcVlanInv" => _("Invalid value for source VLAN"),
            "flash_srcVlanReq" => _("Source VLAN type required"),
            "flash_destReq" => _("A destination is required"),
            "flash_dstVlanInv" => _("Invalid value for destination VLAN"),
            "flash_dstVlanReq" => _("Destination VLAN type required")
        );

        $this->setArgsToScript($argsToScript);
        $this->addScript("flows");
        $this->setInlineScript("flows_add");

        $argsToBody = new stdClass();
        $argsToBody->bandwidthTip = "(" . $min . ", " . ($min + $div) . ", " . ($min + 2 * $div) . ", " . ($min + 3 * $div) . ", ... , " . $max . ")";

        $argsToBody->res_wizard = (Common::hasSessionVariable('res_wizard')) ? TRUE : FALSE;

        $domain = new domain_info();
        $allDomains = $domain->fetch(FALSE);

        $domains = array();
        foreach ($allDomains as $d) {
            $dom = new stdClass();
            $dom->id = $d->dom_id;
            $dom->name = $d->dom_descr;
            $domains[] = $dom;
        }
        $argsToBody->domains = $domains;

        $this->setArgsToBody($argsToBody);

        $this->render();
    }
    
    public function add() {

        $src_urn = Common::POST("src_urn");
        $dst_urn = Common::POST("dst_urn");

        if ($src_urn && $dst_urn) {

            $new_flow = new flow_info();

            $meican = new meican_info();

            $new_flow->src_meican_id = $meican->getLocalMeicanId();
            $new_flow->src_urn_string = $src_urn;

            $new_flow->dst_meican_id = $meican->getLocalMeicanId();
            $new_flow->dst_urn_string = $dst_urn;

            if (Common::POST("vlan_options")) {
                if (Common::POST("sourceVLANType") == "FALSE") {
                    // src VLAN untagged
                    $new_flow->src_vlan = 0;
                } else {
                    // src VLAN tagged
                    $new_flow->src_vlan = (Common::POST("src_vlan")) ? (Common::POST("src_vlan")) : "any";
                }

                if (Common::POST("destVLANType") == "FALSE") {
                    // dst VLAN untagged
                    $new_flow->dst_vlan = 0;
                } else {
                    // dst VLAN tagged
                    $new_flow->dst_vlan = (Common::POST("dst_vlan")) ? (Common::POST("dst_vlan")) : "any";
                }
            }
            if ($path = Common::POST("path")) {
                $new_flow->path = $path;
            }

            return $new_flow->insert();
        } else
            return FALSE;
    }

//    public function add() {
//        $flowData = $_POST["flowData"];
//
//        /**
//         * @param $flowData
//         * 1 -> name
//         * 2 -> bandwidth
//         * 3 -> source domainId
//         * 4 -> source URN
//         * 5 -> source VLAN
//         * 6 -> destination domainId
//         * 7 -> destination URN
//         * 8 -> destination VLAN
//         */
//        $flow = new flow_info();
//
//        $flow->flw_name = $flowData[1];
//        $flow->bandwidth = $flowData[2];
//
//        $flow->src_dom = $flowData[3];
//        $flow->src_urn_string = $flowData[4];
//        $flow->src_vlan = $flowData[5];
//
//        $flow->dst_dom = $flowData[6];
//        $flow->dst_urn_string = $flowData[7];
//        $flow->dst_vlan = $flowData[8];
//
//        $result = $flow->insert();
//
//        if ($result) {
//            if (Common::hasSessionVariable('res_wizard')) {
//                $res = new reservations();
//                $res->update_flow($result->flw_id);
//                $res->setFlash(_("Flow") . " '$result->flw_name' " . _("added"), 'success');
//                $res->page1();
//            } else {
//                $this->setFlash(_("Flow") . " '$result->flw_name' " . _("added"), 'success');
//                $this->show();
//            }
//        } else {
//            $this->setFlash(_("Fail to create flow"), 'error');
//            $this->add_form();
//        }
//    }

    public function edit($flow_id_array) {
        $min = 100;
        $max = 1000;
        $div = 100;

        $flowId = NULL;
        if (array_key_exists('flw_id', $flow_id_array)) {
            $flowId = $flow_id_array['flw_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $flow_info = new flow_info();
        $flow_info->flw_id = $flowId;
        $flow = $flow_info->getFlowDetails();

        if (!$flow) {
            $this->setFlash(_("Flow not found or could not get endpoints information"), "fatal");
            $this->show();
            return;
        }

        $endpoint = "http://{$flow->source->dom_ip}/".Framework::$systemDirName."/main.php?app=topology&services&wsdl";
        $ws = new nusoap_client($endpoint, array('cache_wsdl' => 0));
        $src_networks = $ws->call('getURNDetails', array());

        if (!$src_networks) {
            $this->setFlash(_("Could not get source topology"), "fatal");
            $this->show();
            return;
        }

        $dst_networks = NULL;
        if ($flow->source->dom_id == $flow->dest->dom_id) {
            $dst_networks = $src_networks;
        } else {
            $endpoint = "http://{$flow->dest->dom_ip}/".Framework::$systemDirName."/main.php?app=topology&services&wsdl";
            $ws = new nusoap_client($endpoint, array('cache_wsdl' => 0));
            $dst_networks = $ws->call('getURNDetails', array());

            if (!$dst_networks) {
                $this->setFlash(_("Could not get destination topology"), "fatal");
                $this->show();
                return;
            }
        }

        $argsToScript = array(
            "src_networks_edit" => $src_networks,
            "dst_networks_edit" => $dst_networks,
            "src_network_id" => $flow->source->net_id,
            "src_device_id" => $flow->source->dev_id,
            "src_port" => $flow->source->port,
            "src_vlan" => $flow->source->vlan,
            "dst_network_id" => $flow->dest->net_id,
            "dst_device_id" => $flow->dest->dev_id,
            "dst_port" => $flow->dest->port,
            "dst_vlan" => $flow->dest->vlan,
            "band_min" => $min,
            "band_max" => $max,
            "band_div" => $div,
            "flash_nameReq" => _("A name is required"),
            "flash_bandInv" => _("Invalid value for bandwidth"),
            "flash_sourceReq" => _("A source is required"),
            "flash_srcVlanInv" => _("Invalid value for source VLAN"),
            "flash_srcVlanReq" => _("Source VLAN type required"),
            "flash_destReq" => _("A destination is required"),
            "flash_dstVlanInv" => _("Invalid value for destination VLAN"),
            "flash_dstVlanReq" => _("Destination VLAN type required")
        );

        $this->setArgsToScript($argsToScript);
        $this->addScript("flows");
        $this->setInlineScript("flows_edit");

        $this->action = 'edit';

        $argsToBody = new stdClass();
        $argsToBody->bandwidthTip = "(" . $min . ", " . ($min + $div) . ", " . ($min + 2 * $div) . ", " . ($min + 3 * $div) . ", ... , " . $max . ")";

        $argsToBody->res_wizard = (Common::hasSessionVariable('res_wizard')) ? TRUE : FALSE;

        $dom = new domain_info();
        $allDomains = $dom->fetch(FALSE);
        $domains = array();
        foreach ($allDomains as $d) {
            unset($dom);
            $dom->id = $d->dom_id;
            $dom->name = $d->dom_descr;
            $domains[] = $dom;
        }
        $argsToBody->domains = $domains;

        $argsToBody->flow = $flow;

        $this->setArgsToBody($argsToBody);

        $this->render();
    }

    public function update() {
        $flowData = $_POST["flowData"];

        /**
         * @param $flowData
         * 0 -> id
         * 1 -> name
         * 2 -> bandwidth
         * 3 -> source domainId
         * 4 -> source URN
         * 5 -> source VLAN
         * 6 -> destination domainId
         * 7 -> destination URN
         * 8 -> destination VLAN
         */
        $flow = new flow_info();

        $flow->flw_id = $flowData[0];
        $flow->flw_name = $flowData[1];
        $flow->bandwidth = $flowData[2];

        $flow->src_dom = $flowData[3];
        $flow->src_urn_id = $flowData[4];
        $flow->src_vlan = $flowData[5];

        $flow->dst_dom = $flowData[6];
        $flow->dst_urn_id = $flowData[7];
        $flow->dst_vlan = $flowData[8];

        if ($flow->update()) {
            if (Common::hasSessionVariable('res_wizard')) {
                $res = new reservations();
                $res->update_flow($flow->flw_id);
                $res->setFlash(_("Flow") . " '$flow->flw_name' " . _("updated"), "success");
                $res->page1();
            } else {
                $this->setFlash(_("Flow") . " '$flow->flw_name' " . _("updated"), "success");
                $this->show();
            }
        } else {
            $this->setFlash(_("No change has been made"), "warning");
            $this->edit(array("flw_id" => $flow->flw_id));
        }
    }

    public function delete() {
        $del_flows = Common::POST("del_checkbox");

        if ($del_flows) {
            foreach ($del_flows as $flowId) {
                $flow = new flow_info();
                $flow->flw_id = $flowId;
                $tmp = $flow->fetch();
                $result = $tmp[0];
                if ($flow->delete())
                    $this->setFlash(_("Flow") . " '$result->flw_name' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }

    public function get_domain() {
        $domain_id = $_POST['domain_id'];

        $domain = new domain_info();
        $domain->dom_id = $domain_id;
        $dom = $domain->fetch(FALSE);
        $endpoint = "http://{$dom[0]->dom_ip}/".Framework::$systemDirName."/main.php?app=topology&services&wsdl";

        $networks = FALSE;
        if ($ws = new nusoap_client($endpoint, array('cache_wsdl' => 0)))
            $networks = $ws->call('getURNDetails', array());

        $this->setLayout('empty');
        $this->setAction('ajax');
        $this->setArgsToBody($networks);
        $this->render();

    }

//
//      OBSOLET FUNCTION
//
//    public function get_urn() {
//        $info = $_POST["info"];
//
//        $device = $info[1];
//        $port = $info[2];
//
//        $res = MeicanTopology::getURN($info[0], $device, $port);
//
//        $this->setLayout('empty');
//        $this->setAction('ajax');
//        $this->setArgsToBody($res);
//        $this->render();
//    }

    public function add_options() {
        $this->setAction('add_options');
        $this->setArgsToBody(Common::hasSessionVariable('res_wizard'));
        $this->render();
    }
    
}

?>
