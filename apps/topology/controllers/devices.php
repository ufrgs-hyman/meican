<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/meican_controller.php';
include_once 'apps/topology/models/topology.php';
include_once 'apps/topology/models/domain_info.php';
include_once 'apps/topology/models/device_info.php';
include_once 'apps/topology/models/network_info.php';

include_once 'apps/topology/controllers/networks.php';
include_once 'apps/topology/controllers/domains.php';

include_once 'libs/acl_loader.php';

class devices extends MeicanController {

    public $modelClass = 'device_info';

    public function beforeFilter(){
        $this->addScriptForLayout(array('devices'));
    }

    protected function renderEmpty(){
        $this->set(array(
            'title' => _("Devices"),
            'message' => sprintf(_("No %s created"), _("device")).". "._("Please, click the button below to add a new one")
            ));
        parent::renderEmpty();
    }

    public function show() {
        if ($allDevices = $this->makeIndex()) {
            $devices = array();
            $acl = AclLoader::getInstance();
            
            foreach ($allDevices as $d) {
                $device = new stdClass();
                $device->id = $d->dev_id;
                $device->descr = $d->dev_descr;
                $device->ip = $d->dev_ip;
                $device->trademark = $d->trademark;
                $device->model = $d->model;
                $device->nr_ports = $d->nr_ports;
                $device->node_id = $d->node_id;
                
                $device->nr_endpoints = 0;
                $aco = new Acos($d->dev_id,"device_info");
                if ($aco_obj = $aco->fetch(FALSE)) {
                    $children = $aco_obj[0]->findChildren();
                    foreach ($children as $child) {
                        if ($child->model == "urn_info")
                            $device->nr_endpoints++;
                    }
                }

                $tmp = new network_info();
                $tmp->net_id = $d->net_id;
                $result = $tmp->fetch();
                $device->network = $result[0]->net_descr;
                
                $device->deletable = $acl->checkACL('delete', 'device_info', $d->dev_id);
                $device->editable = $acl->checkACL('update', 'device_info', $d->dev_id);

                $devices[] = $device;
            }
            $this->setArgsToBody($devices);
        }
    }

    public function add_form() {
        $dom_info = new domain_info();

        if ($allDomains = $dom_info->fetch()) {
            $domains = array();

            foreach ($allDomains as $dom) {
                $domain = new stdClass();
                $domain->id = $dom->dom_id;
                $domain->descr = $dom->dom_descr;
                $domain->networks = MeicanTopology::getNetworks($dom->dom_id);

                if ($domain->networks)
                    $domains[] = $domain;
            }

            if ($domains) {
                $args = new stdClass();
                $args->domains = $domains;

                $this->setArgsToBody($args);

                $this->setArgsToScript(array(
                    "flash_nameReq" => _("A name is required"),
                    "flash_ipAddrReq" => _("An IP address is required"),
                    "flash_networkReq" => _("A network is required"),
                    "domains" => $domains
                ));

                $this->render('add');
            } else {
                /**
                 * @todo
                 * ao invés de redirecionar e mostrar mensagem, criar link para adicionar a rede e voltar para este form
                 */
                $net_cont = new networks();
                $net_cont->setFlash(_("No network added, you should first add a network before adding a device"), "warning");
                $net_cont->add_form();
            }
        } else {
            /**
             * @todo
             * ao invés de redirecionar e mostrar mensagem, criar link para adicionar o domínio e voltar para este form
             */
            $dom_cont = new domains();
            $dom_cont->setFlash(_("No domain added, you should first add a domain and network before adding a device"), "warning");
            $dom_cont->add_form();
        }
    }

    public function add() {
        $dev_descr = Common::POST("dev_descr");
        $ip_addr = Common::POST("ip_addr");
        $trademark = Common::POST("trademark");
        $model = Common::POST("model");
        $nr_ports = Common::POST("nr_ports");
        $network = Common::POST("network");
        
        $net = new network_info();
        $net->net_id = $network;
        $net_res = $net->fetch();

        $device = new device_info();
        $device->dev_descr = $dev_descr;
        $device->dev_ip = $ip_addr;
        $device->trademark = $trademark;
        $device->model = $model;
        $device->nr_ports = $nr_ports;
        $device->net_id = $network;
        $device->node_id = Common::POST("node_id");
        
        if ($device->insert($network, "network_info")) {
            $this->setFlash(_("Device")." '$device->dev_descr' "._("added in network")." '{$net_res[0]->net_descr}'", "success");
            $this->show();
            return;
        } else $this->setFlash(_("Fail to create device"), "error");

        $this->add_form();
    }
    
    public function edit($dev_id_array) {
        $devId = NULL;
        if (array_key_exists('dev_id', $dev_id_array)) {
            $devId = $dev_id_array['dev_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $dev_info = new device_info();
        $dev_info->dev_id = $devId;
        $device = $dev_info->fetch();

        if (!$device) {
            $this->setFlash(_("Device not found"), "fatal");
            $this->show();
            return;
        }
        
        $dom_info = new domain_info();

        $domains = array();
        if ($allDomains = $dom_info->fetch()) {
            foreach ($allDomains as $dom) {
                $domain = new stdClass();
                $domain->id = $dom->dom_id;
                $domain->descr = $dom->dom_descr;
                $domain->networks = MeicanTopology::getNetworks($dom->dom_id);

                if ($domain->networks)
                    $domains[] = $domain;
            }
        }

        if (!$domains) {
            /**
             * @todo
             * ao invés de redirecionar e mostrar mensagem, criar link para adicionar o domínio e voltar para este form
             */
            $dom_cont = new domains();
            $dom_cont->setFlash(_("No domain added, you should first add a domain and network before adding a device"), "warning");
            $dom_cont->add_form();
        }

        $args = new stdClass();
        $args->device = $device[0];
        $args->domains = $domains;

        $this->setArgsToScript(array(
            "flash_nameReq" => _("A name is required"),
            "flash_ipAddrReq" => _("An IP address is required"),
            "flash_networkReq" => _("A network is required"),
            "domains" => $domains
        ));

        $this->setArgsToBody($args);
        $this->render();
    }
    
    public function update($dev_id_array) {
        $devId = NULL;
        if (array_key_exists('dev_id', $dev_id_array)) {
            $devId = $dev_id_array['dev_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->edit($dev_id_array);
            return;
        }

        $device = new device_info();
        $device->dev_id = $devId;
        $device->dev_descr = Common::POST("dev_descr");
        $device->dev_ip = Common::POST("ip_addr");
        $device->trademark = Common::POST("trademark");
        $device->model = Common::POST("model");
        $device->nr_ports = Common::POST("nr_ports");
        //$device->net_id = Common::POST("network"); -> não puxa pois está desabilitado
        $device->node_id = Common::POST("node_id");
        
        if ($device->update()) {
            $this->setFlash(_("Device")." '$device->dev_descr' "._("updated"), "success");
            $this->show();
            return;
        } else $this->setFlash(_("No change has been made"), "warning");

        $this->edit($dev_id_array);
    }

    public function delete() {
        $del_devs = Common::POST('del_checkbox');

        if ($del_devs) {
            foreach ($del_devs as $devId) {
                $device = new device_info();
                $device->dev_id = $devId;
                $tmp = $device->fetch();
                $result = $tmp[0];
                if ($device->delete())
                    $this->setFlash(_("Device") . " '$result->dev_descr' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }

}

?>
