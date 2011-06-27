<?php

defined ('__FRAMEWORK') or die ("Invalid access.");

include_once 'libs/controller.php';

include_once 'apps/domain/models/urn_info.inc';
include_once 'apps/domain/models/network_info.inc';
include_once 'apps/domain/models/device_info.inc';
include_once 'apps/domain/models/topology.inc';

class urns extends Controller {

    public function urns() {
        $this->app = 'domain';
        $this->controller = 'urns';
        $this->defaultAction = 'show';
    }

    public function show() {

        $urn_info = new urn_info();
        $allUrns = $urn_info->fetch();

        if ($allUrns) {
            $urns = array();

            foreach ($allUrns as $u) {
                $urn = new stdClass();
                $urn->id = $u->urn_id;
                $urn->string = $u->urn_string;

                $net = new network_info();
                $net->net_id = $u->net_id;
                $res = $net->fetch(FALSE);
                $urn->net_id = $res[0]->net_id;
                $urn->network = $res[0]->net_descr;

                $dev = new device_info();
                $dev->dev_id = $u->dev_id;
                $res = $dev->fetch(FALSE);
                $urn->dev_id = $res[0]->dev_id;
                $urn->device = $res[0]->dev_descr;

                $urn->port = $u->port;
                $urn->vlan = $u->vlan;
                $urn->max_capacity = $u->max_capacity;
                $urn->min_capacity = $u->min_capacity;
                $urn->granularity = $u->granularity;
                $urns[] = $urn;
            }
            $this->setAction('show');

            $this->setArgsToBody($urns);

            $networks = Topology::getNetworks();

            $this->setArgsToScript(array(
                "str_no_newUrn" => _("No new URN found in network topology, the system database is updated"),
                "str_error_import" => _("An error has occurred while trying to import the topology"),
                "str_delete_urn" => _("Delete URN?"),
                "str_urn_deleted" => _("URN deleted"),
                "str_urn_not_deleted" => _("Fail to delete URN"),
                "fillMessage" => _("Please fill in all the fields"),
                "confirmMessage" => _("Save modifications?"),
                "duplicateMessage" => _("A URN has been selected more than once"),
                "networks" => $networks
            ));

            $this->addScript('urns');
            $this->setInlineScript('urns_init');
        } else {
            $this->setAction('empty');

            $args = new stdClass();
            $args->title = _("URNs (Uniform Resource Name)");
            $args->message = _("No URN added, click the button bellow to import from topology");
            $this->setArgsToBody($args);
        }

        $this->render();
    }
    
    public function add_form() {
        $this->import();
    }
    
    public function import() {
        $urns = Topology::getURNTopology();
        $networks = Topology::getNetworks();
        
        $args = new stdClass();
        $args->urns = $urns;
        $args->networks = $networks;
        
        if ($urns)
            $this->setArgsToBody($args);
        else {
            $this->setFlash("deu pau");
        }

        $this->setArgsToScript(array(
            "str_no_newUrn" => _("No new URN found in network topology, the system database is updated"),
            "str_delete_urn" => _("Delete URN?"),
            "fillMessage" => _("Please fill in all the fields"),
            "confirmMessage" => _("Save modifications?"),
            "duplicateMessage" => _("A URN has been selected more than once"),
            "networks" => $networks,
            "urns_to_import" => $urns
        ));

        $this->addScript('urns');
        $this->setInlineScript('urns_import');
        
        $this->setAction('import');
        $this->render();
    }

    public function update() {
        $updated = NULL;
        $added = NULL;

        $updated = $this->modify(Common::POST("urn_editArray"));
        $added = $this->add(Common::POST("urn_newArray"));
        
        if ($updated || $added)
            $this->setFlash(_("URN updated"), 'success');

        $this->show();
    }

    private function add($URNData) {
        $cont = 0;
        if ($URNData) {
            foreach ($URNData as $ud) {
                $urn = new urn_info();
                $urn->net_id = $ud[0];
                $urn->dev_id = $ud[1];
                $urn->port = $ud[2];
                $urn->vlan = $ud[3];
                $urn->urn_string = $ud[4];
                $urn->max_capacity = $ud[5];
                $urn->min_capacity = $ud[6];
                $urn->granularity = $ud[7];

                if ($urn->insert($urn->dev_id, "device_info"))
                    $cont++;
            }
        }
        return $cont;
    }

    private function modify($URNData) {
        $cont = 0;
        if ($URNData) {
            foreach ($URNData as $ud) {
                $urn = new urn_info();
                $urn->urn_id = $ud[0];
                $urn->net_id = $ud[1];
                $urn->dev_id = $ud[2];

                if ($urn->update())
                    $cont++;
            }
        }
        return $cont;
    }

    public function get_topology() {
        $urns = Topology::getURNTopology();
        $this->setLayout('empty');
        $this->setAction('ajax');
        $this->setArgsToBody($urns);
        $this->render();
    }

    public function singleDelete() {
        $del_urn = Common::POST('urnId');

        if ($del_urn) {
            $urn = new urn_info();
            $urn->urn_id = $del_urn;
            $result = $urn->delete();
            $this->setArgsToBody($result);
        }
        
        $this->setLayout('empty');
        $this->setAction('ajax');

        $this->render();
    }

    public function delete() {
        $del_urns = Common::POST("del_checkbox");

        if ($del_urns) {
            foreach ($del_urns as $urnId) {
                $urn = new urn_info();
                $urn->urn_id = $urnId;
                $tmp = $urn->fetch();
                $result = $tmp[0];
                if ($urn->delete())
                    $this->setFlash(_("URN") . " '$result->urn_string' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }

}

?>