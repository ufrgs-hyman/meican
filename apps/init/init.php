<?php

include_once 'libs/application.php';

class init extends Application {

    public function init() {
        $this->appName = 'init';
        $this->defaultController = 'login';
    }
    
    public function getMenu(){
        return array(1 => new MenuItem(array(
            'label' => _("Dashboard"),
            'url' => array('app' => $this->appName, 'controller' => 'gui', 'action' => 'welcome'),
        )));
    }

}

?>