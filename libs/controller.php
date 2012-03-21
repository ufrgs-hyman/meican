<?php

include_once 'libs/view.php';

class Controller extends Object {

    protected $layout = "default";
    protected $argsToBody = NULL;
    protected $scripts = array();
    //private $inlineScript = NULL;
    public $app;
    public $controller = NULL;
    public $action = NULL;
    protected $defaultAction;
    public $viewVars = array('scripts_for_layout' => array());
    public $name = null;
    public $output = '';
    
    public function __construct(){
        
		if ($this->name === null) {
			$this->name = substr(get_class($this), 0, strlen(get_class($this)) -10);
		}
        parent::__construct();
    }

    public function render($action=null) {
        if (empty($action))
            $action = $this->action;
        if ($this->layout === 'default' && $this->isAjax())
            $this->layout .= '_ajax';
        $view = new View($this->app, $this->controller, $action, $this->layout);
		$view->set($this->viewVars);
        $view->setArgs($this->argsToBody);
        //$view->script->setArgs($this->argsToScript);
        //$view->script->setScriptFiles($this->scripts);
        //$view->script->setInlineScript($this->inlineScript);
        $this->output .= $view->build();
        echo $this->output;
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
            if (Configure::read('Asset.compress'))
                $this->viewVars['scripts_for_layout'][]="{$this->app}/cjs/$script.js";
            else
                $this->viewVars['scripts_for_layout'][]="apps/{$this->app}/webroot/js/$script.js";
    }

    public function setFlash($message, $status='info') {
        $this->viewVars['content_for_flash'][] = "$status:$message";
    }

    protected function setArgsToBody($args) {
        $this->argsToBody = $args;
    }

    protected function setArgsToScript($args) {
        $this->set('scripts_vars', $args);
    }
    
    public function setAction($action) {
        $this->action = $action;
    }

    public function getDefaultAction() {
        return $this->defaultAction;
    }
    
    public function renderJson($contents, $options = null){
        echo $this->output .= json_encode($contents, $options);
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
    
    
/**
 * Dispatches the controller action.  Checks that the action
 * exists and isn't private.
 *
 * @param CakeRequest $request
 * @return mixed The resulting response.
 * @throws PrivateActionException, MissingActionException
 */
	public function invokeAction($params) {
        if (empty($params['action']))
            $params['action'] = $this->getDefaultAction();
		try {
            $this->app = $params['app'];
            $this->action = $params['action'];
            $this->name = $this->controller = $params['controller'];
            $this->params = $params;
			$method = new ReflectionMethod($this, $params['action']);
            $privateAction = (
                $method->name[0] === '_' ||
                !$method->isPublic() /*||
                !in_array($method->name,  $this->methods)*/
            );
			if ($privateAction) {
				throw new PrivateActionException(array(
					'controller' => $this->name . "Controller",
					'action' => $params['action']
				));
			}
			return $method->invokeArgs($this, array($params['pass'])); //TODO: tirar esse array

		} catch (ReflectionException $e) {
			throw new MissingActionException(array(
				'controller' => $this->name . "Controller",
				'action' => $params['action']
			));
		}
	}

}
