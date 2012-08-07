<?php

include_once 'libs/view.php';

class Controller extends Object {

    protected $layout = "default";
    protected $scripts = array();
    //private $inlineScript = NULL;
    public $app;
    public $controller = NULL;
    public $action = NULL;
    protected $defaultAction;
    public $viewVars = array('scripts_for_layout' => array());
    public $name = null;
    public $output = '';
    public $autoRender = true;
    public $request = null;
    public $response = null;

    public function __construct($request, $response) {
        $this->request = $request;
        $this->response = $response;
        if ($this->name === null) {
            $this->name = substr(get_class($this), 0, strlen(get_class($this)) - 10);
        }
        parent::__construct();
    }

    public function render($action=null, $params = array()) {
        if (empty($action))
            $action = $this->action;
        if ($this->layout === 'default' && $this->isAjax())
            $this->layout .= '_ajax';
        $params = array_merge(array(
            'app' =>$this->app,
            'controller' => $this->controller,
            'layout' => $this->layout
        ), $params);
        $view = new View($params['app'], $params['controller'], $action, $params['layout']);
        $view->set($this->viewVars);
        $this->autoRender = false;
        $output = $view->build();
        $this->output .= $output;
        echo $output;
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

    protected function addScriptForLayout($script) {
        if (is_array($script))
            foreach ($script as $item)
                $this->addScriptForLayout($item);
        else
        if (Configure::read('Asset.compress'))
            $this->viewVars['scripts_for_layout'][] = "{$this->app}/cjs/$script.js";
        else
            $this->viewVars['scripts_for_layout'][] = "apps/{$this->app}/webroot/js/$script.js";
    }

    public function setFlash($message, $status='info') {
        $this->viewVars['content_for_flash'][] = "$status:$message";
    }

    protected function setArgsToBody($args) {
        $this->set('bodyArgs', $args);
    }

    protected function setArgsToScript($args) {
        $this->set('scripts_vars', $args);
    }

    public function renderJson($contents, $options = null) {
        $this->autoRender = false;
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
    public function invokeAction(CakeRequest $request) {
        if (empty($request->params['action']))
            $request->params['action'] = $this->defaultAction;
        try {
            $this->app = $request->params['app'];
            $this->action = $request->params['action'];
            $this->name = $this->controller = $request->params['controller'];
            $this->params = $request->params;
            $method = new ReflectionMethod($this, $request->params['action']);
            $privateAction = (
                    $method->name[0] === '_' ||
                    !$method->isPublic() /* ||
                      !in_array($method->name,  $this->methods) */
                    );
            if ($privateAction) {
                throw new PrivateActionException(array(
                    'controller' => $this->name . "Controller",
                    'action' => $request->params['action']
                ));
            }
            return $method->invokeArgs($this, array($request->params['pass'])); //TODO: tirar esse array
        } catch (ReflectionException $e) {
            throw new MissingActionException(array(
                'controller' => $this->name . "Controller",
                'action' => $request->params['action']
            ));
        }
    }
    
    /**
 * Redirects to given $url, after turning off $this->autoRender.
 * Script execution is halted after the redirect.
 *
 * @param mixed $url A string or array-based URL pointing to another location within the app,
 *     or an absolute URL
 * @param integer $status Optional HTTP status code (eg: 404)
 * @param boolean $exit If true, exit() will be called after the redirect
 * @return mixed void if $exit = false. Terminates script if $exit = true
 */
	public function redirect($url, $status = null, $exit = true) {
		$this->autoRender = false;

		if (is_array($status)) {
			extract($status, EXTR_OVERWRITE);
		}

		if (function_exists('session_write_close')) {
			session_write_close();
		}
/*
		if (!empty($status) && is_string($status)) {
			$codes = array_flip($this->response->httpCodes());
			if (isset($codes[$status])) {
				$status = $codes[$status];
			}
		}*/

		if ($url !== null) {
            $url = array_merge(array('app' => $this->app, 'controller' => $this->controller), $url);
            header("Location: ".Dispatcher::getInstance()->url($url));
		}

		/*if (!empty($status) && ($status >= 300 && $status < 400)) {
			$this->response->statusCode($status);
		}*/

		if ($exit) {
			exit();
		}
	}
    
    

    /**
     * Verifies if a value is a integer, otherwise, flashes a error and redirects to index
     * @param unknown_type $id
     */
    protected function validId($id=null) {
        if (empty($id) || !(is_int($id) || preg_match('/^[0-9]*$/', $id)))
            return false;
        return true;
    }

}
