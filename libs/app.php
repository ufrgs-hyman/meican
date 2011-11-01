<?php

class MenuItem {
    var $label;
    var $url;
    var $sub = array();
    var $model = null;
    var $right = 'read';
    
    public function __construct($args){
      foreach($args as $key => $arg)
          $this->$key = $arg;
    }
    
    public static function filter($preMenus = array()){
        if (empty($preMenus))
            return $preMenus;
        $menus = array();
        $acl = AclLoader::getInstance();
        foreach ($preMenus as $k => $preMenu)
            if (empty($preMenu->model) || $acl->checkACL($preMenu->right, $preMenu->model)){
                $preMenu->sub = self::filter($preMenu->sub);
                if (!empty($preMenu->sub) || !empty($preMenu->url))
                    $menus[$k] = $preMenu;
            }
        ksort($menus);
        return $menus;
    }
}

class App {
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
    
    public static function getAllMenus(){
        $apps = array('aaa', 'bpm', 'circuits', 'init', 'topology');
        $menus = array();
        foreach ($apps as $app){
            $appObj = self::factory($app);
            if ($appObj)
                $menus += $appObj->getMenu();//array_merge($appObj->getMenu(), $menus);
        }
        $menus = MenuItem::filter($menus);
        return $menus;
    }
    
    // Método Factory parametrizado
    public static function factory($app, $args=array())
    {
        if (file_exists("apps/$app/$app.php") &&
                include_once "apps/$app/$app.php") {
            return new $app($args);
        } else {
            throw new Exception ('Driver não encontrado');
        }
    }
    
}

?>