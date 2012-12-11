<?php

include_once 'libs/application.php';

class bpm extends Application {

    public function bpm() {
        $this->appName = 'bpm';
        $this->defaultController = 'requests';
    }

    public function getMenu() {
        return array(40 => 
            new MenuItem(array(
                'label' => _("Workflows"),
                'model' => 'bpm','domain_info',
                'url' => array('app' => $this->appName, 'controller' => 'policyEditor')
            ))
            );
    }

    function getDashboard() {
        return array(
            60 => new MenuItem(array(
                'label' => _("Requests"),
                'model' => 'bpm',
                'url' => array('app' => $this->appName, 'controller' => 'requests'),
                'image' => 'webroot/img/requests_1.png'
            )),
        );
    }

}

?>