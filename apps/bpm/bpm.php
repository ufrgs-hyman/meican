<?php

include_once 'libs/app.php';

class bpm extends App {

    public function bpm() {
        $this->appName = 'bpm';
        $this->defaultController = 'requests';
    }

    public function getMenu() {
        return array(40 => new MenuItem(array(
                'label' => _("BPM"),
                'sub' => array(
                    new MenuItem(array(
                        'label' => _("Requests"),
                        'model' => 'request_info',
                        'url' => array('app' => $this->appName, 'controller' => 'requests')
                    )),
                    new MenuItem(array(
                        'label' => _("ODE"),
                        'model' => 'request_info',
                        'url' => array('app' => $this->appName, 'controller' => 'ode')
                    )),
                )
            )));
    }

    function getDashboard() {
        return array(
            60 => new MenuItem(array(
                'label' => _("Requests"),
                'model' => 'request_info',
                'url' => array('app' => $this->appName, 'controller' => 'requests'),
                'image' => 'webroot/img/requests_1.png'
            )),
        );
    }

}

?>