<?php

defined ('__FRAMEWORK') or die ("Invalid access.");

include_once 'libs/controller.php';

include_once 'apps/domain/models/device_info.inc';
include_once 'apps/domain/models/network_info.inc';
include_once 'apps/domain/controllers/networks.php';

include_once 'libs/acl_loader.inc';

class devices extends Controller {

    public function devices() {
        $this->app = 'domain';
        $this->controller = 'devices';
        $this->defaultAction = 'show';
    }

    public function show() {

        $dev = new device_info();
        $allDevices = $dev->fetch();

        if ($allDevices) {
            $devices = array();
            $acl = new AclLoader();
            
            foreach ($allDevices as $d) {
                $device = new stdClass();
                $device->id = $d->dev_id;
                $device->descr = $d->dev_descr;
                $device->ip = $d->dev_ip;
                $device->trademark = $d->trademark;
                $device->model = $d->model;
                $device->nr_ports = $d->nr_ports;
                $device->latitude = ($d->dev_lat) ? $d->dev_lat : "-";
                $device->longitude = ($d->dev_lng) ? $d->dev_lng : "-";

                $tmp = new network_info();
                $tmp->net_id = $d->net_id;
                $result = $tmp->fetch();
                $device->network = $result[0]->net_descr;
                
                $device->deletable = $acl->checkACL('delete', 'device_info', $d->dev_id);
                $device->editable = $acl->checkACL('update', 'device_info', $d->dev_id);

                $devices[] = $device;
            }
            $this->setAction('show');
            
            $this->setArgsToBody($devices);
        } else {
            $this->setAction('empty');

            $args = new stdClass();
            $args->title = _("Devices");
            $args->message = _("No device added, click the button below to add a new one");
            $this->setArgsToBody($args);
        }
        
        $this->render();
    }

    public function add_form() {
        $net_info = new network_info();
        $networks = $net_info->fetch();

        if ($networks) {
            $this->setAction('add');
            
            $args = new stdClass();
            $args->networks = $networks;
            
            $this->setArgsToBody($args);
            
            $this->setArgsToScript(array(
                "flash_nameReq" => _("A name is required"),
                "flash_ipAddrReq" => _("An IP address is required"),
                "flash_networkReq" => _("A network is required"),
            ));
            
            $this->addScript('devices');
            
            $this->render();
        } else {
            /**
             * @todo
             * ao invés de redirecionar e mostrar mensagem, criar link para adicionar a rede e voltar para este form
             */
            $net_cont = new networks();
            $net_cont->setFlash(_("No network added, you should first add a network before adding a device"), "warning");
            $net_cont->add_form();
        }
    }

    public function add() {
        $dev_descr = Common::POST("dev_descr");
        $ip_addr = Common::POST("ip_addr");
        $trademark = Common::POST("trademark");
        $model = Common::POST("model");
        $nr_ports = Common::POST("nr_ports");
        $network = Common::POST("network");

        $device = new device_info();
        $device->dev_descr = $dev_descr;
        $device->dev_ip = $ip_addr;
        $device->trademark = $trademark;
        $device->model = $model;
        $device->nr_ports = $nr_ports;
        $device->net_id = $network;
        $device->dev_lat = Common::POST("dev_lat");
        $device->dev_lng = Common::POST("dev_lng");

        if ($device->insert($network, "network_info")) {
            $this->setFlash(_("Device")." '$device->dev_descr' "._("added"), "success");
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

        $net_info = new network_info();
        $networks = $net_info->fetch();

        if (!$networks) {
            /**
             * @todo
             * ao invés de redirecionar e mostrar mensagem, criar link para adicionar a rede e voltar para este form
             */
            $net_cont = new networks();
            $net_cont->setFlash(_("No network added, you should first add a network before editing a device"), "warning");
            $net_cont->add_form();
            return;
        }

        $args = new stdClass();
        $args->device = $device[0];
        $args->networks = $networks;

        $this->setArgsToScript(array(
            "flash_nameReq" => _("A name is required"),
            "flash_ipAddrReq" => _("An IP address is required"),
            "flash_networkReq" => _("A network is required"),
        ));

        $this->addScript('devices');

        $this->setArgsToBody($args);
        $this->setAction('edit');
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
        $device->dev_lat = Common::POST("dev_lat");
        $device->dev_lng = Common::POST("dev_lng");
        $device->net_id = Common::POST("network");

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
