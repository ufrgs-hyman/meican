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

    public function render() {
        //modificar para referenciar direto controller, nao passando os parametros para o construtor
        $view = new View($this->app, $this->controller, $this->action);

        if ($this->app != 'init') {
            Common::recordVar('last_view', "app=$this->app&controller=$this->controller&action=$this->action");
            Common::setSessionVariable('welcome_loaded', 1);
        }
//        $teste = ::rescueVar('last_view');
//        if ($teste === FALSE) {
//            $app = Framework::getMainApp();
//            $teste = "app=$app";
//        }
//        debug("last view", $teste);

        $view->setArgs($this->argsToBody);
        $view->script->setArgs($this->argsToScript);
        $view->script->setScriptFiles($this->scripts);
        $view->script->setInlineScript($this->inlineScript);
        if ($this->layout === 'default' && $this->isAjax())
            $this->layout .= '_ajax';
        $view->set('content_for_flash', $this->flash? $this->flash : '');
        $view->layout = $this->layout;
        echo $view->build();
    }

    public function isAjax() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    protected function addScript($script) {
        $this->scripts[] = "apps/$this->app/views/scripts/$script.js?1";
    }

    protected function setInlineScript($script) {
        $this->inlineScript = "apps/$this->app/views/scripts/$script.js?1";
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

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public function getLayout() {
        return $this->layout;
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

}

?>
