<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'libs/auth.php';

class info_box extends Controller {

    public function info_box() {
        $this->app = 'init';
        $this->controller = 'info_box';
        $this->defaultAction = 'show';
        $this->setLayout('info_box');
    }

    public function show() {
        $args = new stdClass();
        $args->usr_login = AuthSystem::getUserLogin();
        $args->system_time = date("d/m/Y H:i");
        
        $this->setArgsToBody($args);
        $this->render();
    }
    
    public function time() {
        $this->setLayout('empty');
        $this->action = 'time';
        $this->render();
    }

}


?>
