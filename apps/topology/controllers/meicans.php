<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/meican_controller.php';
include_once 'apps/topology/models/meican_info.php';

class meicans extends MeicanController {

    public $modelClass = 'meican_info';
    public $app = 'topology';

    protected function renderEmpty(){
        $this->set(array(
            'title' => _("MEICANs"),
            'message' => sprintf(_("No %s created"), _("MEICAN")).". "._("Please, click the button below to add a new one")
            ));
        parent::renderEmpty();
    }

    public function show() {
        if ($allMeicans = $this->makeIndex(array('useACL' => false))) {
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
        }
    }
    
    public function add_form() {
        $this->render('form');
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
    
    public function edit($params = array()) {
        if ($this->validId($params[0])) {
            $mecId = $params[0];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            return $this->show();
        }

        $mec_info = new meican_info();
        $mec_info->meican_id = $mecId;
        $meican = $mec_info->fetch(FALSE);

        if (!$meican) {
            $this->setFlash(_("MEICAN not found"), "fatal");
            $this->show();
            return;
        }
        
        $this->set('meican', $meican[0]);
        $this->render('form');
    }
    
    public function update($params = array()) {
        if ($this->validId($params[0])) {
            $mecId = $params[0];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            return $this->show();
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
        
        $this->edit($params);
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
                else
                    $this->setFlash(_("Error"), 'error');
            }
        }
        $this->show();
    }
    
}

?>
