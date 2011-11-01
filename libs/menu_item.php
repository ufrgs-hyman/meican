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
    
    public static function getAllMenus(){
        $apps = array('aaa', 'bpm', 'circuits', 'init', 'topology'); //TODO: detectar automaticamente apps instaladas
        $menus = array();
        foreach ($apps as $app){
            $appObj = App::factory($app);
            if ($appObj)
                $menus += $appObj->getMenu();//array_merge($appObj->getMenu(), $menus);
        }
        $menus = self::filter($menus);
        return $menus;
    }
}

?>