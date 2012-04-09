<?php

include_once 'libs/application.php';

class bpm extends Application {

    public function bpm() {
        $this->appName = 'bpm';
        $this->defaultController = 'requests';
    }

    public function getMenu() {
        return array(40 => new MenuItem(array(
                'label' => _("Requests"),
                'sub' => array(
                    new MenuItem(array(
                        'label' => _("Authorization"),
                        'model' => 'bpm',
                        'url' => array('app' => $this->appName, 'controller' => 'requests')
                    )),
                    new MenuItem(array(
                        'label' => _("ODE"),
                        'model' => 'bpm',
                        'url' => array('app' => $this->appName, 'controller' => 'ode')
                    )),
                )
            )));
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