<?php

include_once 'libs/menu_item.php';

class Application {
    protected $appName;
    protected $defaultController;

    public function getDefaultController() {
        return $this->defaultController;
    }

    public function loadController($controller) {
        if (file_exists("apps/$this->appName/controllers/$controller.php")) {
            include_once "apps/$this->appName/controllers/$controller.php";

            if (class_exists($controller)){
                return new $controller;
            }
        }
        return FALSE;
    }

    public function getAppName(){
        return $this->appName;
    }
    
    /**
     * Return a array of menus of the current application.
     * Return something like:
     * new MenuItem(array(
                    'label' => '_("Users")',
                    'model' => 'group_info',
                    'right' => 'read',
                    'url' => array('app' => $this->appName, 'controller' => 'users', 'action' => 'show'),
                    'sub' => array(new MenuItem(....))
                ));
     * @return type 
     */
    public function getMenu(){
        return array();
    }
    
    
    /**
     * Return a array of dashboard items of the current application.
     * Return something like:
     * new MenuItem(array(
                    'label' => '_("Users")',
                    'model' => 'group_info',
                    'right' => 'read',
                    'url' => array('app' => $this->appName, 'controller' => 'users', 'action' => 'show'),
                    'image' => '',
                ));
     * @return type 
     */
    public function getDashboard(){
        return array();
    }
    
    // Método Factory parametrizado
    public static function factory($app, $args=array())
    {
        Language::getInstance()->setDomain($app);
        if (file_exists("apps/$app/$app.php") &&
                include_once "apps/$app/$app.php") {
            return new $app($args);
        } else {
            throw new Exception ('App not found');
            return False;
        }
    }
    
}

?>