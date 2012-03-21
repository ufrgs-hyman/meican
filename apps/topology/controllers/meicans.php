<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/controller.php';

include_once 'apps/topology/models/meican_info.php';

class meicans extends Controller {

    public function meicans() {
        $this->app = 'topology';
        $this->controller = 'meicans';
        $this->defaultAction = 'show';
    }

    public function show() {

        $mec = new meican_info();
        $allMeicans = $mec->fetch(FALSE);

        if ($allMeicans) {
            $meicans = array();

            foreach ($allMeicans as $m) {
                $meican = new stdClass();
                $meican->id = $m->meican_id;
                $meican->descr = $m->meican_descr;
                $meican->ip = $m->meican_ip;
                $meican->dir_name = $m->meican_dir_name;
                $meican->local = ($m->local_domain) ? _("Yes") : _("No");

                $meicans[] = $meican;
            }
            $this->setArgsToBody($meicans);
            $this->render('show');
        } else {
            $args = new stdClass();
            $args->title = _("MEICANs");
            $args->message = _("No MEICAN is registered, click the button below to register a new one");
            $this->setArgsToBody($args);
            $this->render('empty');
        }
    }
    
    public function add_form() {
        $this->render('add');
    }
    
    public function add() {
        $meican_descr = Common::POST("meican_descr");
        $meican_ip = Common::POST("meican_ip");

        if ($meican_descr && $meican_ip) {
            $meican = new meican_info();
            $meican->meican_descr = $meican_descr;
            $meican->meican_ip = $meican_ip;
            $meican->meican_dir_name = Common::POST("meican_dir_name");
            $meican->local_domain = (Common::POST("local_domain")) ? 1 : 0;

            if ($meican->insert()) {
                $this->setFlash(_("MEICAN")." '$meican->meican_descr' "._("added"), "success");
                $this->show();
                return;
            } else $this->setFlash(_("Fail to create MEICAN"), "error");

        } else $this->setFlash(_("Missing arguments"), "error");

        $this->add_form();
    }
    
    public function edit($mec_id_array) {
        $mecId = NULL;
        if (array_key_exists('meican_id', $mec_id_array)) {
            $mecId = $mec_id_array['meican_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $mec_info = new meican_info();
        $mec_info->meican_id = $mecId;
        $meican = $mec_info->fetch(FALSE);

        if (!$meican) {
            $this->setFlash(_("MEICAN not found"), "fatal");
            $this->show();
            return;
        }
        
        $this->setArgsToBody($meican[0]);
        $this->render('edit');
    }
    
    public function update($mec_id_array) {
        $mecId = NULL;
        if (array_key_exists('meican_id', $mec_id_array)) {
            $mecId = $mec_id_array['meican_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }
        
        $meican_descr = Common::POST("meican_descr");
        $meican_ip = Common::POST("meican_ip");

        if ($meican_descr && $meican_ip) {
            $meican = new meican_info();
            $meican->meican_id = $mecId;
            $meican->meican_descr = $meican_descr;
            $meican->meican_ip = $meican_ip;
            $meican->meican_dir_name = Common::POST("meican_dir_name");
            $meican->local_domain = (Common::POST("local_domain")) ? 1 : 0;

            if ($meican->update()) {
                $this->setFlash(_("MEICAN")." '$meican->meican_descr' "._("updated"), "success");
                $this->show();
                return;
            } else $this->setFlash(_("No change has been made"), "warning");

        } else $this->setFlash(_("Missing arguments"), "error");
        
        $this->edit($mec_id_array);
    }
    
    public function delete() {
        if ($del_mecs = Common::POST('del_checkbox')) {
            foreach ($del_mecs as $mecId) {
                $meican = new meican_info();
                $meican->meican_id = $mecId;
                $tmp = $meican->fetch(FALSE);
                $result = $tmp[0];
                if ($meican->delete())
                    $this->setFlash(_("MEICAN") . " '$result->meican_descr' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }
    
}

?>