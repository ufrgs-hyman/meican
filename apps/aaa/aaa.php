<?php

include_once 'libs/app.php';

class aaa extends App {

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

}

?>