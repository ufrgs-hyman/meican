<?php

include_once'libs/script.php';

class View {

    //public $passedArgs = array();

    private $app = null;
    private $controller = null;
    private $action = null;
    private $view = null;   //file to build body
    public $bodyContent = NULL; //result of build body
    //private $scriptContent;
    //private $header=array();   //array of files to build header
    //private $headerContent; //result of build header
    private $bodyArgs;
    //private $scriptArgs;
    public $script;


    public function View($app, $controller, $action) {
        $this->app = $app;
        $this->controller = $controller;
        $this->action = $action;

        $this->script = new Script();
    }

    public function build() {
        $this->buildBody();

        if ($this->script->jsFiles || $this->script->scriptArgs)
            $this->script->build();
    }

    public function setArgs($args) {
        $this->bodyArgs = $args;
    }

    private function setView() {
        if ($this->action)
            $this->view = "apps/$this->app/views/$this->controller" . '_' . "$this->action.php";
        else
            $this->view = "apps/$this->app/views/$this->controller.php";
    }

    public function buildBody() {
        $this->setView();
        $this->passedArgs = $this->bodyArgs;

        if (file_exists($this->view)) {
            ob_start();
            include($this->view);
            $this->bodyContent = ob_get_contents();
            ob_end_clean();
        } else
            $this->bodyContent = NULL;

        if ($this->bodyContent)
            return TRUE;
        else
            return FALSE;
    }

    public function url($url = array()){
        return $this->buildLink($url);
    }

    /**
     *
     * @param <array> valid indexes (all optional - default values are the application values): app, controller, action, param
     * Example:
     * @param <array> $argsArray : $argsArray[app] = appName, $argsArray[controller] = controllerName, $argsArray[action] = actionName, $argsArray[param] = paramVal
     * @return <string> $url : "main.php?app=appName&controller=controllerName&action=actionName&param=paramVal"
     * <string> paramVal valid sintax: "ind1:val1/ind2:val2"
     */
    public function buildLink($argsArray=array()) {
        if (is_string($argsArray))
            return Dispatcher::getInstance()->url($argsArray);
        else
            return $url = Dispatcher::getInstance()->url(array_merge(
                            array(
                                'app' => $this->app,
                                'controller' => $this->controller
                            ), $argsArray));
        /* $url = "main.php";

          $url .= (array_key_exists('app', $argsArray)) ? "?app=".$argsArray['app'] : "?app=$this->app";
          $url .= (array_key_exists('controller', $argsArray)) ? "&controller=".$argsArray['controller'] : "&controller=$this->controller";

          if (array_key_exists('action', $argsArray)) {
          $url .= "&action=".$argsArray['action'];
          } elseif ($this->action) {
          $url .= "&action=$this->action";
          } else return $url;

          if (array_key_exists('param', $argsArray)){
          $param = $argsArray['param'];
          $url .= "&param="; //usr_id:5,dom_src:aasds
          unset($str);
          if (is_array($param)){
          foreach ($param as $ind => $val){
          $str[] = "$ind:$val";
          }
          $url .= implode(',',$str);

          } else $url .= $param;
          }
          return $url; */
    }

    public function addElement($elementName, $argsToElement=null) {

        if ($this->app) {
            include("apps/$this->app/views/elements/$elementName.php");
            return true;
        } else {
            printf("SET APP CONF");
            return false;
        }
    }

}

?>
