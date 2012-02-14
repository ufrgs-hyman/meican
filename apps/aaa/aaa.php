<?php

include_once 'libs/application.php';

class aaa extends Application {

    public function aaa() {
        $this->appName = 'aaa';
        $this->defaultController = 'users';
    }

    public function getMenu() {
        return array(30 => new MenuItem(array(
                'label' => _("Users"),
                'sub' => array(
                    new MenuItem(array(
                        'label' => _("Users"),
                        'model' => 'group_info',
                        'url' => array('app' => $this->appName, 'controller' => 'users')
                    )),
                    new MenuItem(array(
                        'label' => _("Groups"),
                        'model' => 'group_info',
                        'url' => array('app' => $this->appName, 'controller' => 'groups')
                    )),
                    new MenuItem(array(
                        'label' => _("Access control"),
                        'model' => 'acl',
                        'url' => array('app' => $this->appName, 'controller' => 'acl')
                    )),
                )
            )));
    }

    function getDashboard() {
        return array(
            40 => new MenuItem(array(
                'label' => _("Users"),
                'model' => 'group_info',
                'url' => array('app' => $this->appName, 'controller' => 'users'),
                'image' => 'webroot/img/management.png'
            )),
        );
    }

}

?>