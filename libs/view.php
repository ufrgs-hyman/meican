<?php

//include_once'libs/script.php';

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
    //private $scriptArgs;
    //public $script;
    public $layout = 'default';
    public $viewVars = array();
    public $extension = '.php';

    public function View($app, $controller, $action, $layout = 'default') {
        $this->app = $app;
        $this->controller = $controller;
        $this->action = $action;
        $this->layout = $layout;
        if ($this->app)
            $this->view = "apps/$this->app/views/";
        else
            $this->view = "layouts/";
        if ($this->controller)
            $this->view .= $this->controller;
        if ($this->controller && $this->action)
            $this->view .= '_';
        if ($this->action)
            $this->view .= $this->action;
        $this->view .= $this->extension;
    }

    public function build() {
        if ($this->layout === 'empty')
            return $this->bodyContent = $this->buildView($this->view);
        else
            return $this->bodyContent = $this->buildView("layouts/$this->layout.php", array(
                'content_for_body' => $this->buildView($this->view),
                    ));
    }

    public function buildView($view=null, $vars=array()) {
        if (file_exists($view)) {
            $dom = Language::getInstance()->getDomain();
            Language::getInstance()->setDomain(isset($vars['app'])?$vars['app']: $this->app);
            ob_start();
            extract(array_merge($this->viewVars, $vars), EXTR_SKIP);
            $this->passedArgs = isset($bodyArgs)?$bodyArgs:null; //@deprecated TODO: do not use 
            include($view);
            $return = ob_get_contents();
            ob_end_clean();
            Language::getInstance()->setDomain($dom);
        } else {
            throw new MissingViewException($view);
            $return = null; //TODO: trigger error
        }
        return $return;
    }

    public function url($url = '') {
        return $this->buildLink($url);
    }

    /**
     *
     * @param <array> valid indexes (all optional - default values are the application values): app, controller, action, param
     * Example:
     * @param <array> $argsArray : $argsArray[app] = appName, $argsArray[controller] = controllerName, $argsArray[action] = actionName, $argsArray[param] = paramVal
     * @return <string> $url : "{base}/{appName}/{controllerName}/{actionName}/{paramVal}"
     * <string> paramVal valid sintax: "ind1:val1/ind2:val2"
     */
    public function buildLink($argsArray='') {
        if (!is_array($argsArray))
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
            throw Exception("app not set: ".$this->app);
            return false;
        }
    }

    public function element($element, $args=array()) {
        if (!empty($args['app']))
            $app = $args['app'];
        else
            $app = $this->app;
        $element = "apps/$app/views/elements/$element";
        if (strstr('.', $element) === false)
            $element .= ".php";
        return $this->buildView($element, $args);
    }

    /**
     * Allows a template or element to set a variable that will be available in
     * a layout or other element. Analagous to Controller::set.
     *
     * @param mixed $one A string or an array of data.
     * @param mixed $two Value in case $one is a string (which then works as the key).
     *    Unused if $one is an associative array, otherwise serves as the values to $one's keys.
     * @return void
     * @access public
     */
    public function set($one, $two = null) {
        $data = null;
        if (is_array($one)) {
            if (is_array($two)) {
                $data = array_combine($one, $two);
            } else {
                $data = $one;
            }
        } else {
            $data = array($one => $two);
        }
        if ($data == null) {
            return false;
        }
        $this->viewVars = $data + $this->viewVars;
    }

    public function scripts() {
        if (!empty($this->viewVars['scripts_vars']))
            $scripts_vars = $this->viewVars['scripts_vars'];
        else
            $scripts_vars = array();
        $ret = '';
        if (!empty($scripts_vars)) {
            foreach ($scripts_vars as $name => $val) {
                if (is_string($val))
                    $ret .= "var $name = '$val';\n";
                else//if (is_array($val) || is_object($val))
                    $ret .= "var $name = " . json_encode($val) . ";\n";
                /*else
                    $ret .= "var $name = $val;\n";*/
            }
            $ret = '<script type="text/javascript">' . $ret . '</script>';
        }
        $ret .= $this->script($this->viewVars['scripts_for_layout']);
        return $ret;
    }
    
    public function script($script = null){
        if (empty($script))
            return ;
        else if (is_array($script)){
            $ret = '';
            foreach ($script as $s)
                $ret .= $this->script($s);
            return $ret;
        } else
            return '<script type="text/javascript" src="' . $this->url($script) . '"></script>';            
    }

}
