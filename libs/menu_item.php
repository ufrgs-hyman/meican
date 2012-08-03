<?php


include_once 'libs/acl_loader.php';

class MenuItem {
    var $label;
    var $url;
    var $sub = array();
    var $model = null;
    var $right = 'read';
    var $image = null;
    
    public function __construct($args){
      foreach($args as $key => $arg)
          $this->$key = $arg;
    }
    
    public static function filter($preMenus = array()){
        if (empty($preMenus))
            return $preMenus;
        $menus = array();
        $acl = AclLoader::getInstance();
        foreach ($preMenus as $k => $preMenu) {
            if (is_array($preMenu->model)) {
                $allowAll = TRUE;
                foreach ($preMenu->model as $model) {
                    $allowAll &= $acl->checkACL($preMenu->right, $model);
                }
                if ($allowAll) {
                    $preMenu->sub = self::filter($preMenu->sub);
                    if (!empty($preMenu->sub) || !empty($preMenu->url))
                        $menus[$k] = $preMenu;
                }
            } elseif (empty($preMenu->model) || $acl->checkACL($preMenu->right, $preMenu->model)){
                $preMenu->sub = self::filter($preMenu->sub);
                if (!empty($preMenu->sub) || !empty($preMenu->url))
                    $menus[$k] = $preMenu;
            }
        }
        ksort($menus);
        return $menus;
    }
    
    public static function getAllMenus($method = 'getMenu') {
        $apps = Configure::read('apps');
        $menus = array();
        $dom = Language::getInstance()->getDomain();
        foreach ($apps as $app){
            $appObj = Application::factory($app);
            if ($appObj) {
                //Language::getInstance()->setDomain($app);
                $menus += $appObj->{$method}();//array_merge($appObj->getMenu(), $menus);
            }
        }
        Language::getInstance()->setDomain($dom);
        $menus = self::filter($menus);
        return $menus;
    }
    
}
