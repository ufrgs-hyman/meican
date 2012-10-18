<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/meican_controller.php';
include_once 'apps/topology/models/domain_info.php';

class domains extends MeicanController {

    public $modelClass = 'domain_info';
    public $app = 'topology';

    public function beforeFilter(){
        $this->addScriptForLayout(array('domains'));
    }

    protected function renderEmpty(){
        $this->set(array(
            'title' => _("Domains"),
            'message' => sprintf(_("No %s created"), _("domain")).". "._("Please, click the button below to add a new one")
            ));
        parent::renderEmpty();
    }

    public function show() {
        if ($allDomains = $this->makeIndex()) {
            $domains = array();

            foreach ($allDomains as $d) {
                $domain = new stdClass();
                $domain->id = $d->dom_id;
                $domain->descr = $d->dom_descr;
                $domain->oscars_ip = $d->oscars_ip;
                $domain->oscars_protocol = $d->oscars_protocol;
                $domain->idc_url = $d->idc_url;
                $domain->topology_id = $d->topology_id;
                $domain->dom_version = $d->dom_version;

                $domains[] = $domain;
            }
            $this->setArgsToBody($domains);
            $this->render('show');
        }
    }

    public function add_form() {
        $this->render('add');
    }

    public function add() {
        $dom_descr = Common::POST("dom_descr");
        if ($dom_descr) {
            $domain = new domain_info();
            $domain->dom_descr = $dom_descr;
            $domain->idc_url = Common::POST("idc_url");
            $domain->oscars_ip = Common::POST("oscars_ip");
            $domain->oscars_protocol = Common::POST("oscars_protocol");
            $domain->topology_id = Common::POST("topology_id");
            
            $domain->ode_wsdl_path = Common::POST("ode_wsdl_path");
            $domain->ode_start = Common::POST("ode_start");
            $domain->ode_response = Common::POST("ode_response");
            
            $domain->dom_version = Common::POST("dom_version");
     
            if ($domain->insert(NULL, "topology")) {
                $this->setFlash(_("Domain")." '$domain->dom_descr' "._("added"), "success");
                $this->show();
                return;
            } else $this->setFlash(_("Fail to create domain"), "error");

        } else $this->setFlash(_("Missing arguments"), "error");

        $this->add_form();
    }
    
    public function edit($dom_id_array) {
        $domId = NULL;
        if (array_key_exists('dom_id', $dom_id_array)) {
            $domId = $dom_id_array['dom_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $dom_info = new domain_info();
        $dom_info->dom_id = $domId;
        $domain = $dom_info->fetch(FALSE);

        if (!$domain) {
            $this->setFlash(_("Domain not found"), "fatal");
            $this->show();
            return;
        }
        
        $this->setArgsToBody($domain[0]);
        $this->render('edit');
    }
    
    public function update($dom_id_array) {
        $domId = NULL;
        if (array_key_exists('dom_id', $dom_id_array)) {
            $domId = $dom_id_array['dom_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->edit($dom_id_array);
            return;
        }
        $dom_descr = Common::POST("dom_descr");

        if ($dom_descr) {

            $domain = new domain_info();
            $domain->dom_id = $domId;
            $domain->dom_descr = $dom_descr;
            $domain->idc_url = Common::POST("idc_url");
            $domain->oscars_ip = Common::POST("oscars_ip");
            $domain->oscars_protocol = Common::POST("oscars_protocol");
            $domain->topology_id = Common::POST("topology_id");
            
            $domain->ode_wsdl_path = Common::POST("ode_wsdl_path");
            $domain->ode_start = Common::POST("ode_start");
            $domain->ode_response = Common::POST("ode_response");
            
            $domain->dom_version = Common::POST("dom_version");
            
            if ($domain->update()) {
                $this->setFlash(_("Domain")." '$domain->dom_descr' "._("updated"), "success");
                $this->show();
                return;
            } else $this->setFlash(_("No change has been made"), "warning");

        } else $this->setFlash(_("Missing arguments"), "error");
        
        $this->edit($dom_id_array);
    }

    public function delete() {
        if ($del_doms = Common::POST('del_checkbox')) {
            foreach ($del_doms as $domId) {
                $domain = new domain_info();
                $domain->dom_id = $domId;
                $tmp = $domain->fetch();
                $result = $tmp[0];
                if ($domain->delete())
                    $this->setFlash(_("Domain") . " '$result->dom_descr' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }

}

?>
