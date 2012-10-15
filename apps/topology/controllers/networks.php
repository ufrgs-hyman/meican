<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/meican_controller.php';
include_once 'apps/aaa/models/group_info.php';

include_once 'apps/topology/models/domain_info.php';
include_once 'apps/topology/models/network_info.php';
include_once 'apps/topology/models/device_info.php';

class networks extends MeicanController {

    public $modelClass = 'network_info';
    public $app = 'topology';

    public function beforeFilter(){
        $this->addScriptForLayout(array('networks'));
    }

    protected function renderEmpty(){
        $this->set(array(
            'title' => _("Networks"),
            'message' => sprintf(_("No %s created"), _("network")).". "._("Please, click the button below to add a new one")
            ));
        parent::renderEmpty();
    }

    public function show() {
        if ($allNets = $this->makeIndex()) {
            $networks = array();

            foreach ($allNets as $n) {
                $network = new stdClass();
                $network->id = $n->net_id;
                $network->descr = $n->net_descr;
                $network->latitude = $n->net_lat;
                $network->longitude = $n->net_lng;
                
                $aco = new Acos($n->net_id, "network_info");
                $network->parent_domain = _("Could not find domain");
                if ($parent = $aco->getParentNodes()) {
                    if ($parent[0]->model == "domain_info") {
                        $dom_tmp = new domain_info();
                        $dom_tmp->dom_id = $parent[0]->obj_id;
                        if ($ret = $dom_tmp->fetch(FALSE))
                            $network->parent_domain = $ret[0]->dom_descr;
                    }
                }

                $tmp = new device_info();
                $tmp->net_id = $n->net_id;
                $devices = $tmp->fetch(FALSE);
                if ($devices) {
                    $dev = array();
                    foreach ($devices as $d)
                        $dev[] = $d->dev_descr;
                    $network->devices = implode("<br>", $dev);
                } else
                    $network->devices = _("No device added to this network");

                $networks[] = $network;
            }
            $this->setArgsToBody($networks);
        }
        $this->render('show');
    }

    public function add_form() {
        //$this->redirect(array('action' => 'show'));
        $domain_info = new domain_info();
        $domains = $domain_info->fetch();
        
        $args = new stdClass();
        $args->domains = ($domains) ? $domains : NULL;
        
        $argsToSpt = array(
            "flash_nameReq" => _("A name is required"),
            "flash_setLatLng" => _("Set coordinates"),
            "flash_setDomain" => _("Select a domain")
        );
        
        $this->setArgsToScript($argsToSpt);
        
        $this->setArgsToBody($args);
        $this->render('add');
    }

    public function add() {
        $net_descr = Common::POST("net_descr");
        $parent_domain = Common::POST("domain");
        $net_lat = Common::POST("net_lat");
        $net_lng = Common::POST("net_lng");
        
        
        if ($net_descr && ($parent_domain != -1)) {
            $network = new network_info();
            $network->net_descr = $net_descr;

            if (!$network->fetch(FALSE)) { // verifica se o nome está disponível
                $network->net_lat = $net_lat;
                $network->net_lng = $net_lng;

                if ($network->insert($parent_domain, "domain_info")) {
                    $this->setFlash(_("Network") . " '$network->net_descr' " . _("added"), "success");
                    return $this->show();
                    //return $this->redirect(array('action' => 'show'));
                } else
                    $this->setFlash(_("Fail to create network"), "error");
            } else
                $this->setFlash(_("Network") . " '$net_descr' " . _('already exists'), "error");
        } else
            $this->setFlash(_("Missing argument"), "error");

        $this->add_form();
    }
    
    public function edit($net_id_array) {
        $netId = NULL;
        if (array_key_exists('net_id', $net_id_array)) {
            $netId = $net_id_array['net_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $net_info = new network_info();
        $net_info->net_id = $netId;
        $network = NULL;
        if ($res_network = $net_info->fetch(FALSE)) {
            $network = $res_network[0];
        } else {
            $this->setFlash(_("Network not found"), "fatal");
            return $this->show();
            //return $this->redirect(array('action' => 'show'));
        }
        
        $aco = new Acos($network->net_id, "network_info");
        $parents = $aco->getParentNodes();
        
        if ($parents[0]->model == "domain_info")
            $network->parent_domain = $parents[0]->obj_id;
        
        $domain_info = new domain_info();
        $domains = $domain_info->fetch(false);
        
        $args = new stdClass();
        $args->domains = ($domains) ? $domains : NULL;
        $args->network = $network;
        
        $this->setArgsToBody($args);
        $this->render('edit');
    }
    
    public function update($net_id_array) {
        $netId = NULL;
        if (array_key_exists('net_id', $net_id_array)) {
            $netId = $net_id_array['net_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->edit($net_id_array);
            return;
        }
        
        $net_descr = Common::POST("net_descr");
        $parent_domain = Common::POST("domain");

        if ($net_descr && ($parent_domain != -1)) {
            $network = new network_info();
            $network->net_id = $netId;
            $network->net_descr = $net_descr;
            $network->net_lat = Common::POST("net_lat");
            $network->net_lng = Common::POST("net_lng");
            
            if ($network->update()) {
                $this->setFlash(_("Network")." '$network->net_descr' "._("updated"), "success");
                $this->show();
                return;
            } else $this->setFlash(_("No change has been made"), "warning");

        } else $this->setFlash(_("Missing arguments"), "error");
        
        $this->edit($net_id_array);
    }

    public function delete() {
        $del_nets = Common::POST('del_checkbox');

        if ($del_nets) {
            foreach ($del_nets as $netId) {
                $network = new network_info();
                $network->net_id = $netId;
                $tmp = $network->fetch(FALSE);
                $result = $tmp[0];
                if ($network->delete())
                    $this->setFlash(_("Network") . " '$result->net_descr' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }

}

?>
