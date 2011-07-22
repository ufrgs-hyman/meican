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
        
        $dom = new domain_info();
        if ($allDomains = $dom->fetch()) {
        //if (false) {
            $domains = array();

            foreach ($allDomains as $d) {
                $domain = new stdClass();
                $domain->id = $d->dom_id;
                $domain->descr = $d->dom_descr;
                $domain->ip = $d->oscars_ip;
                $domain->topo_id = $d->topo_domain_id;
                
                $domain->urns = Topology::getURNs($domain->id);
                //$domain->networks = Topology::getNetworks($domain->id);

                $domains[] = $domain;
            }

            $this->setAction('show');

            $this->setArgsToBody($domains);

            $this->setArgsToScript(array(
                "str_no_newUrn" => _("No new URN found in network topology, the system database is updated"),
                "str_error_import" => _("An error has occurred while trying to import the topology"),
                "str_delete_urn" => _("Delete URN?"),
                "str_urn_deleted" => _("URN deleted"),
                "str_urn_not_deleted" => _("Fail to delete URN"),
                "fillMessage" => _("Please fill in all the fields"),
                "confirmMessage" => _("Save modifications?"),
                "duplicateMessage" => _("A URN has been selected more than once")
                //"networks" => $domains
            ));

            $this->addScript('urns');
            $this->setInlineScript('urns_init');
        } else {
            $this->setAction('empty');

            $args = new stdClass();
            $args->title = _("URNs (Uniform Resource Name)");
            $args->message = _("Before adding a URN, you need to register at least one OSCARS domain, click the button bellow to register a new one");
            $args->link = array("controller" => "domains", "action" => "add_form");
            $this->setArgsToBody($args);
        }

        $this->render();
    }
    
    public function import_option() {
        $dom = new domain_info();
        $allDomains = $dom->fetch();

        if ($allDomains) {
            $domains = array();

            foreach ($allDomains as $d) {
                $domain = new stdClass();
                $domain->id = $d->dom_id;
                $domain->descr = $d->dom_descr;
                $domain->oscars_ip = $d->oscars_ip;
                $domain->topo_domain_id = $d->topo_domain_id;

                $domains[] = $domain;
            }

            $this->setArgsToBody($domains);
        }
        
        $this->setAction('import_option');
        $this->render();
    }
    
    public function import($dom_id_array) {
        $domId = NULL;
        if (array_key_exists('dom_id', $dom_id_array)) {
            $domId = $dom_id_array['dom_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }
        
        $dom = new domain_info();
        $dom->dom_id = $domId;
        $domain = $dom->fetch();
        
        $urns = Topology::getURNTopology($domId);
        //Framework::debug("urns", $urns);
        //$this->import_option();
        //return false;
        
        $networks = Topology::getNetworks($domId);
        //Framework::debug("net",$networks);
        //$this->import_option();
        //return false;
        
        $args = new stdClass();
        $args->urns = $urns;
        $args->networks = $networks;
        $args->domain = $domain[0]->dom_descr;
        
        if ($urns)
            $this->setArgsToBody($args);
        else {
            $this->setFlash("deu pau");
        }

        $this->setArgsToScript(array(
            "fillMessage" => _("Please fill in all the fields"),
            "confirmMessage" => _("Save modifications?"),
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