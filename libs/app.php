<?php

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
    
}

?>
