<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'includes/auth.inc';

class info_box extends Controller {

    public function info_box() {
        $this->app = 'init';
        $this->controller = 'info_box';
        $this->defaultAction = 'show';
        $this->setLayout('info_box');
    }

    public function show() {
        $userLogin = AuthSystem::getUserLogin();
        $this->setArgsToBody($userLogin);
        $this->render();
    }

}


?>
