<?php

include_once 'libs/view.php';

class Controller {

    private $layout = "default";
    private $flash = array();
    private $argsToBody = NULL;
    private $argsToScript = NULL;
    //private $argsToHeader = NULL;
    private $scripts = array();
    private $inlineScript = NULL;
    public $app;
    public $controller = NULL;
    public $action = NULL;
    protected $defaultAction;
    public $viewVars = array('scripts_for_layout' => array());

    public function render($action=null) {
        //modificar para referenciar direto controller, nao passando os parametros para o construtor
        if (empty($action))
            $action = $this->action;
        if ($this->layout === 'default' && $this->isAjax())
            $this->layout .= '_ajax';
        $view = new View($this->app, $this->controller, $action, $this->layout);

        /*if ($this->app != 'init') {
            Common::recordVar('last_view', "app=$this->app&controller=$this->controller&action=$this->action");
            Common::setSessionVariable('welcome_loaded', 1);
        }*/
//        $teste = ::rescueVar('last_view');
//        if ($teste === FALSE) {
//            $app = Framework::Configure::read('mainApp');
//            $teste = "app=$app";
//        }
//        debug("last view", $teste);
		$view->set($this->viewVars);
        $view->set('content_for_flash', $this->flash? $this->flash : '');
        $view->set('scripts_vars', $this->argsToScript);
        $view->setArgs($this->argsToBody);
        //$view->script->setArgs($this->argsToScript);
        //$view->script->setScriptFiles($this->scripts);
        //$view->script->setInlineScript($this->inlineScript);
        echo $view->build();
    }

    public function isAjax() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    protected function addScript($script) { //@deprecated
        $this->scripts[] = "apps/{$this->app}/webroot/js/$script.js?1";
    }

    protected function setInlineScript($script) {
        //$this->inlineScript = "apps/{$this->app}/webroot/js/$script.js?1";
        $this->addScriptForLayout($script);
    }
    
    protected function addScriptForLayout($script){
    	if (is_array($script))
    		foreach ($script as $item)
    			$this->addScriptForLayout($item);
    	else
	    	$this->viewVars['scripts_for_layout'][]="apps/{$this->app}/webroot/js/$script.js?1";
    }

    public function setFlash($message, $status='info') {
        $this->flash[] = "$status:$message";
        //$this->flash[]->status = $status;
    }

    protected function setArgsToBody($args) {
        $this->argsToBody = $args;
    }

    protected function setArgsToScript($args) {
        $this->argsToScript = $args;
    }

    public function setApp($app) {
        $this->app = $app;
    }

    public function getApp() {
        return $this->app;
    }

    public function setController($controller) {
        $this->controller = $controller;
    }

    public function getController() {
        return $this->controller;
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function getAction() {
        return $this->action;
    }

    public function getDefaultAction() {
        return $this->defaultAction;
    }

    /**
     *
     * @param <string> $paramString : "ind1:val1/ind2:val2"
     * @return <array> $paramArray : $paramArray[ind1] = val1, $paramArray[ind2] = val2
     */
    static function getParam($paramString) {
        $block = explode(',', $paramString);

        $paramArray = array();
        if ($block) {
            foreach ($block as $b) {
                $item = explode(':', $b);
                if (array_key_exists(0, $item) && array_key_exists(1, $item))
                    $paramArray[$item[0]] = $item[1];
            }

            return $paramArray;
        } else
            return FALSE;
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

}

?>
