<?php

defined ('__FRAMEWORK') or die ("Invalid access.");

include_once 'libs/controller.php';

include_once 'apps/domain/models/meican_info.inc';

class meicans extends Controller {

    public function meicans() {
        $this->app = 'domain';
        $this->controller = 'meicans';
        $this->defaultAction = 'show';
    }

    public function show() {

        $fed = new meican_info();
        $allFederations = $fed->fetch(FALSE);

        if ($allFederations) {
            $federations = array();

            foreach ($allFederations as $f) {
                $federation = new stdClass();
                $federation->id = $f->meican_id;
                $federation->descr = $f->meican_descr;
                $federation->ip = $f->meican_ip;
                $federation->dir_name = $f->meican_dir_name;

                $federations[] = $federation;
            }
            Framework::debug("meicans",$federations);
            $this->setAction('show');

            $this->setArgsToBody($federations);
        } else {
            $this->setAction('empty');

            $args = new stdClass();
            $args->title = _("Federations");
            $args->message = _("No federation is registered, click the button below to register a new one");
            $this->setArgsToBody($args);
        }

        $this->render();
    }
    
    public function add_form() {
        $this->setAction('add');
        $this->render();
    }
    
    public function add() {
        $fed_descr = Common::POST("fed_descr");
        $ip_addr = Common::POST("fed_ip");

        if ($fed_descr && $ip_addr) {
            $federation = new meican_info();
            $federation->meican_descr = $fed_descr;
            $federation->meican_ip = $ip_addr;

            if ($federation->insert()) {
                $this->setFlash(_("Federation")." '$federation->meican_descr' "._("added"), "success");
                $this->show();
                return;
            } else $this->setFlash(_("Fail to create federation"), "error");

        } else $this->setFlash(_("Missing arguments"), "error");

        $this->add_form();
    }
    
    public function edit($fed_id_array) {
        $fedId = NULL;
        if (array_key_exists('fed_id', $fed_id_array)) {
            $fedId = $fed_id_array['fed_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $fed_info = new meican_info();
        $fed_info->meican_id = $fedId;
        $federation = $fed_info->fetch(FALSE);

        if (!$federation) {
            $this->setFlash(_("Federation not found"), "fatal");
            $this->show();
            return;
        }
        
        $this->setArgsToBody($federation[0]);
        $this->setAction('edit');
        $this->render();
    }
    
    public function update($fed_id_array) {
        $fedId = NULL;
        if (array_key_exists('fed_id', $fed_id_array)) {
            $fedId = $fed_id_array['fed_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }
        
        $fed_descr = Common::POST("fed_descr");
        $ip_addr = Common::POST("fed_ip");

        if ($fed_descr && $ip_addr) {
            $federation = new meican_info();
            $federation->meican_id = $fedId;
            $federation->meican_descr = $fed_descr;
            $federation->meican_ip = $ip_addr;

            if ($federation->update()) {
                $this->setFlash(_("Federation")." '$federation->meican_descr' "._("updated"), "success");
                $this->show();
                return;
            } else $this->setFlash(_("No change has been made"), "warning");

        } else $this->setFlash(_("Missing arguments"), "error");
        
        $this->edit($fed_id_array);
    }
    
    public function delete() {
        if ($del_feds = Common::POST('del_checkbox')) {
            foreach ($del_feds as $fedId) {
                $federation = new meican_info();
                $federation->fed_id = $fedId;
                $tmp = $federation->fetch(FALSE);
                $result = $tmp[0];
                if ($federation->delete())
                    $this->setFlash(_("Federation") . " '$result->meican_descr' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }
    
}

?>