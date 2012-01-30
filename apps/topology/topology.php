<?php

include_once 'libs/app.php';

class topology extends App {

    public function topology() {
        $this->appName = 'topology';
        $this->defaultController = 'urns';
    }
    
    public function getMenu(){
        return array(20 => new MenuItem(array(
            'label' => _("Topologies"),
            'sub' => array(
                new MenuItem(array(
                    'label' => _("MEICANs"),
                    'model' => 'topology',
                    'url' => array('app' => $this->appName, 'controller' => 'meicans')
                )),
                new MenuItem(array(
                    'label' => _("Domains"),
                    'model' => 'domain_info',
                    'url' => array('app' => $this->appName, 'controller' => 'domains')
                )),
                new MenuItem(array(
                    'label' => _("Networks"),
                    'model' => 'network_info',
                    'url' => array('app' => $this->appName, 'controller' => 'networks')
                )),
                new MenuItem(array(
                    'label' => _("Devices"),
                    'model' => 'device_info',
                    'url' => array('app' => $this->appName, 'controller' => 'devices')
                )),
                new MenuItem(array(
                    'label' => _("URNs"),
                    'model' => array('urn_info','domain_info'),
                    'url' => array('app' => $this->appName, 'controller' => 'urns')
                )),
            )
        )));
    }

}

?>