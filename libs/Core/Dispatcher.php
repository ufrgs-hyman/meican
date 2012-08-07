<?php

include_once 'libs/common.php';
include_once 'libs/application.php';

App::uses('Configure', 'Core');
App::uses('Object', 'Core');
App::uses('Debugger', 'Utility');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');

//include_once 'libs/Model/datasource.php';
include_once 'libs/Core/Language.php';

/**
 * Dispatcher Class. Reads required url and instanciate properly apps, controller and calls an action.
 * Also builds urls.
 */
class Dispatcher {

    public function __construct($defaults = array()) {
        $this->defaults = array_merge(array('app' => 'init', 'controller' => 'gui', 'action' => '', 'pass' => array()));
        $this->base = dirname(env('PHP_SELF'));
        if ($this->base === DS || $this->base === '.') {
            $this->base = '';
        }
        if ($this->base == '/')
            $this->base = '';
        Configure::write('systemDirName', $this->base);
    }

    /**
     * Main function, reads url and call action
     */
    function dispatch(CakeRequest $request, CakeResponse $response, $additionalParams = array()) {
        if (!empty($_SERVER['PATH_INFO']))
            $url = $_SERVER['PATH_INFO'];
        else if (array_key_exists('url', $_GET))
            $url = Common::GET('url');
        else
            $url = null;
        if ($this->login($request->url)) // precisa fazer login?
            return;
        if ($this->asset($request->url))
            return;
        /* if (empty($url)&&!(Common::GET('app')))
          return $this->legacyDispatch(); */
        $params = array_merge($this->parse($request->url), array('verify_login' => true));
        $request->addParams($params);
        
		if (!empty($additionalParams)) {
			$request->addParams($additionalParams);
		}
        //verifica login?
        if ($request->params['verify_login'] && $request->params['controller'] !== 'ws' && !$this->checkLogin()) //TODO: authetication for webservices
            return;
        try {
            if (!($application = Application::factory($request->params['app'])))
                throw new Exception(_("Invalid app"));
            if (empty($request->params['controller']))
                $request->params['controller'] = $application->getDefaultController();
            $controller = $application->loadController($request, null);

            if (!($controller instanceof Controller)) {
                throw new MissingControllerException(array(
                    'class' => $controller . 'Controller'
                ));
            }
            if ($request->params['controller'] !== 'ws')
                $controller->invokeAction($request);
            if ($controller->autoRender) {
                $response = $controller->render();
            }
            
            /*
            $response = $this->_invoke($controller, $request, $response);
            if (isset($request->params['return'])) {
                return $response->body();
            }

            $response->send();*/
        } catch (Exception $e) {
            Debugger::showError(E_WARNING, "Catch dispatcher error");
            Debugger::showError(E_WARNING, $e->getMessage());
            debug($e->getTrace());
        }/*
          if (class_exists('Datasource'))
          Datasource::getInstance()->close(); */
    }
    
    

/**
 * Initializes the components and models a controller will be using.
 * Triggers the controller action, and invokes the rendering if Controller::$autoRender is true and echo's the output.
 * Otherwise the return value of the controller action are returned.
 *
 * @param Controller $controller Controller to invoke
 * @param CakeRequest $request The request object to invoke the controller for.
 * @param CakeResponse $response The response object to receive the output
 * @return CakeResponse te resulting response object
 */
	protected function _invoke(Controller $controller, CakeRequest $request, CakeResponse $response) {
		/*$controller->constructClasses();
		$controller->startupProcess();*/

		$render = true;
		$result = $controller->invokeAction($request);
		if ($result instanceof CakeResponse) {
			$render = false;
			$response = $result;
		}

		if ($render && $controller->autoRender) {
			$response = $controller->render();
		} elseif ($response->body() === null) {
			$response->body($result);
		}
		//$controller->shutdownProcess();

		return $response;
	}

    /**
     * Check wether a user is logged in, otherwise redirects to login page.
     * @return Boolean  
     */
    public function checkLogin() {
        include_once 'libs/auth.php';
        if (AuthSystem::userTryToLogin() || AuthSystem::isUserLoggedIn()) {
            return true;
        } else {
            header('Location: ' . $this->url('login'));
            return false;
        }
    }

    function login($url = null) {
        if ($url !== 'login')
            return false;
        Language::getInstance()->setDomain('init');

        include_once 'apps/init/controllers/login.php';
        $login = new Login();

        if (key_exists('message', $_GET))
            $message = $_GET['message'];
        else
            $message = NULL;

        $login->show($message);
        return true;
    }

    /**
     * Checks if a requested asset exists and sends it to the browser
     *
     * @param string $url Requested URL
     * @param CakeResponse $response The response object to put the file contents in.
     * @return boolean True on success if the asset file was found and sent
     */
    public function asset($url) {
        if (strpos($url, '..') !== false || strpos($url, '.') === false) {
            return false;
        }
        $isCss = (
                strpos($url, 'ccss/') === 0 ||
                preg_match('#^(theme/([^/]+)/ccss/)|(([^/]+)(?<!css)/ccss)/#i', $url)
                );
        $isJs = (
                strpos($url, 'cjs/') === 0 ||
                preg_match('#^/((theme/[^/]+)/cjs/)|(([^/]+)(?<!js)/cjs)/#i', $url)
                );
        if ($isCss || $isJs) {
            include 'assets.php';
            return true;
        }
        $pathSegments = explode('.', $url);
        $ext = array_pop($pathSegments);
        $parts = explode('/', $url);
        $assetFile = null;

        if ($parts[0] === 'theme') {
            $themeName = $parts[1];
            unset($parts[0], $parts[1]);
            $fileFragment = urldecode(implode(DS, $parts));
            $path = App::themePath($themeName) . 'webroot' . DS;
            if (file_exists($path . $fileFragment)) {
                $assetFile = $path . $fileFragment;
            }
        } else {
            $plugin = Inflector::camelize($parts[0]);
            if (CakePlugin::loaded($plugin)) {
                unset($parts[0]);
                $fileFragment = urldecode(implode(DS, $parts));
                $pluginWebroot = CakePlugin::path($plugin) . 'webroot' . DS;
                if (file_exists($pluginWebroot . $fileFragment)) {
                    $assetFile = $pluginWebroot . $fileFragment;
                }
            }
        }

        if ($assetFile !== null) {
            $this->_deliverAsset($response, $assetFile, $ext);
            return true;
        }
        return false;
    }

    /**
     * From a given URL string, extract the array of params (apps, controller, action, params)
     * @param String $url The given URL
     * @return array An array of URL params
     */
    public function parse($url) {
        $val = explode('/', $url);
        if (empty($val[0]))
            array_shift($val);
        $route = array();
        if (count($val) >= 3) {
            $route = array('app' => $val[0], 'controller' => $val[1], 'action' => $val[2]);
            $params = array();
            foreach (array_slice($val, 3) as $par)
                $params += explode(',', $par);
            foreach ($params as $par) {
                $p = explode(':', $par);
                if (count($p) === 1)
                    $route['pass'][] = $p[0];
                else
                    $route['pass'][$p[0]] = $p[1];
            }
        } else if (count($val) == 2) {
            $route = array('app' => $val[0], 'controller' => $val[1]);
        } else if (count($val) == 1) {
            $route = array('app' => $val[0]);
        }
        $route = array_merge($this->defaults, $route);
        return $route;
    }

    /**
     * From a given array of params, builds a URL string
     * @param array $params
     * @return string
     */
    public function url($params=null) {
        if (!is_array($params))
            return $this->base . '/' . $params;
        $url = $this->base . '/' . $params['app'] . '/' . $params['controller'];
        if (!empty($params['action']) && $params['action'] != 'show')
            $url .= '/' . $params['action'];
        if (empty($params['pass']))
            $params['pass'] = array();
        if (!empty($params['param']))
            $params['pass'] = $params['param'];
        if (!empty($params['pass']))
            if (is_array($params['pass'])) {
                $str = array();
                foreach ($params['pass'] as $k => $v)
                    if (is_int($k))
                        $str[] = $v;
                    else
                        $str[] = $k . ':' . $v;
                $url .= '/' . implode(',', $str);
            } else
                $url .= '/' . $params['pass'];
        return $url;
    }

    static function &getInstance() {
        static $instance = array();

        if (!$instance) {
            $instance[0] = & new Dispatcher();
        }
        return $instance[0];
    }

}
