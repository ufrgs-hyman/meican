<?php

defined ('__FRAMEWORK') or die ("Invalid access.");

include_once 'libs/controller.php';

include_once 'apps/domain/models/federation_info.inc';

class federations extends Controller {

    public function federations() {
        $this->app = 'domain';
        $this->controller = 'federations';
        $this->defaultAction = 'show';
    }

    public function show() {

        $fed = new federation_info();
        $allFederations = $fed->fetch(FALSE);

        if ($allFederations) {
            $federations = array();

            foreach ($allFederations as $f) {
                $federation = new stdClass();
                $federation->id = $f->fed_id;
                $federation->descr = $f->fed_descr;
                $federation->ip = $f->fed_ip;

                $federations[] = $federation;
            }
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
            $federation = new federation_info();
            $federation->fed_descr = $fed_descr;
            $federation->fed_ip = $ip_addr;

            if ($federation->insert()) {
                $this->setFlash(_("Federation")." '$federation->fed_descr' "._("added"), "success");
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

        $fed_info = new federation_info();
        $fed_info->fed_id = $fedId;
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
            $federation = new federation_info();
            $federation->fed_id = $fedId;
            $federation->fed_descr = $fed_descr;
            $federation->fed_ip = $ip_addr;

            if ($federation->update()) {
                $this->setFlash(_("Federation")." '$federation->fed_descr' "._("updated"), "success");
                $this->show();
                return;
            } else $this->setFlash(_("No change has been made"), "warning");

        } else $this->setFlash(_("Missing arguments"), "error");
        
        $this->edit($fed_id_array);
    }
    
    public function delete() {
        if ($del_feds = Common::POST('del_checkbox')) {
            foreach ($del_feds as $fedId) {
                $federation = new federation_info();
                $federation->fed_id = $fedId;
                $tmp = $federation->fetch(FALSE);
                $result = $tmp[0];
                if ($federation->delete())
                    $this->setFlash(_("Federation") . " '$result->fed_descr' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }
    
}

?>