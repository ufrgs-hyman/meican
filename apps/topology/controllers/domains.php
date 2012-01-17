<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/controller.php';

include_once 'apps/topology/models/domain_info.php';

class domains extends Controller {

    public function domains() {
        $this->app = 'topology';
        $this->controller = 'domains';
        $this->defaultAction = 'show';
    }

    public function show() {

        $dom = new domain_info();
        $allDomains = $dom->fetch();

        if ($allDomains) {
            $domains = array();

            foreach ($allDomains as $d) {
                $domain = new stdClass();
                $domain->id = $d->dom_id;
                $domain->descr = $d->dom_descr;
                $domain->oscars_ip = $d->oscars_ip;
                $domain->topology_id = $d->topology_id;
                $domain->ode_ip = ($d->ode_ip) ? $d->ode_ip : _("No IP defined");

                $domains[] = $domain;
            }
            $this->setAction('show');

            $this->setArgsToBody($domains);
        } else {
            $this->setAction('empty');

            $args = new stdClass();
            $args->title = _("Domains");
            $args->message = _("No domain is added, click the button below to add a new one");
            $this->setArgsToBody($args);
        }

        $this->render();
    }

    public function add_form() {
        $this->setAction('add');
        $this->render();
    }

    public function add() {
        $dom_descr = Common::POST("dom_descr");

        if ($dom_descr) {
            $domain = new domain_info();
            $domain->dom_descr = $dom_descr;
            $domain->oscars_ip = Common::POST("oscars_ip");
            $domain->topology_id = Common::POST("topology_id");
            $domain->ode_ip = Common::POST("ode_ip");
            $domain->ode_wsdl_path = Common::POST("ode_wsdl_path");

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
        $this->setAction('edit');
        $this->render();
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
            $domain->oscars_ip = Common::POST("oscars_ip");
            $domain->topology_id = Common::POST("topology_id");
            $domain->ode_ip = Common::POST("ode_ip");
            $domain->ode_wsdl_path = Common::POST("ode_wsdl_path");

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
