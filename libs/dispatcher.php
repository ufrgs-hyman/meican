<?php

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo "Unknown error type: [$errno] $errstr<br />\n";
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}

class Dispatcher {

    public function __construct($defaults = array()) {
        $this->defaults = array_merge(array('app' => 'init', 'controller' => 'gui', 'action' => '', 'param' => array()));
        $this->base = dirname($_SERVER['PHP_SELF']);
        if ($this->base == '/')
            $this->base = '';
    }

    function dispatch() {
        //set_error_handler('myErrorHandler');
        if (!empty($_SERVER['PATH_INFO']))
            $url = $_SERVER['PATH_INFO'];
        else if (array_key_exists('url', $_GET))
            $url = Common::GET('url');
        else
            $url = null;
        if ($url === 'login')
            return $this->login();
        /*if (empty($url)&&!(Common::GET('app')))
            return $this->legacyDispatch();*/
        $this->params = $this->parse($url);
        extract($this->params);
        if ($controller !== 'ws' && !$this->checkLogin()) //TODO: authetication for webservices
            return;
        try {
            if (empty($app))
                $app = Framework::getMainApp();
            if (!($app = Framework::loadApp($app)))
                throw new Exception(_("Invalid app"));
            Language::setLang(get_class($app));
            if (empty($controller))
                $controller = $app->getDefaultController();
            if (!($controller = $app->loadController($controller)))
                throw new Exception(_("Invalid controller"));
            if (empty($action))
                $action = $controller->getDefaultAction();
            if (!$action || !method_exists($controller, $action))
                ;//throw new Exception(_("Invalid action"));
            else
                $controller->$action($param);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function checkLogin() {
        if (AuthSystem::userTryToLogin() || AuthSystem::isUserLoggedIn()) {
            return true;
        } else {
            /* $appClass = Common::GET('app');
              $controllerClass = Common::GET('controller');
              if (($appClass == "init") && ($controllerClass == "gui")) {
              // user has expired the session and is trying to reload the gui - refresh or F5
              // redirect to login
              header('Location: index.php?message=Session Expired');

              } else header('HTTP/1.1 402 Timeout'); */
            header('Location: '.$this->url('/login'));
            return false;
        }
    }

    function login(){
        include_once 'apps/init/controllers/login.php';
        Language::setLang('init');

        $login = new Login();

        if (key_exists('message', $_GET))
            $message = $_GET['message'];
        else
            $message = NULL;

        $login->show($message);
    }

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
                    $route['param'][] = $p[0];
                else
                    $route['param'][$p[0]] = $p[1];
            }
        } else if (count($val) == 2)
            $route = array('app' => $val[0], 'controller' => $val[1]);
        else if (count($val) == 1)
            $route = array('app' => $val[0]);
        $route = array_merge($this->defaults, $route);
        return $route;
    }
    
    public function url($params=null) {
        if (!is_array($params))
            return $this->base . '/' . $params;
        $url = $this->base . '/' . $params['app'] . '/' . $params['controller'];
        if (!empty($params['action']))
            $url .= '/' . $params['action'];
        if (!empty($params['param']))
            if (is_array($params['param'])) {
                $str = array();
                foreach ($params['param'] as $k => $v)
                    if (is_int($k))
                        $str[] = $v;
                    else
                        $str[] = $k . ':' . $v;
                $url .= '/' . implode(',', $str);
            } else
                $url .= '/' . $params['param'];
        return $url;
    }

    static function &getInstance() {
        static $instance = array();

        if (!$instance) {
            $instance[0] = & new Dispatcher();
        }
        return $instance[0];
    }

    function legacyDispatch() {
        if (array_key_exists('app', $_GET) && array_key_exists('services', $_GET)) {
            $appClass = Common::GET('app');
            $app = Framework::loadApp($appClass);
            $controller = $app->loadController('ws');
        } else {

            if (AuthSystem::userTryToLogin() || AuthSystem::isUserLoggedIn()) {

                if (array_key_exists('app', $_GET)) {
                    $appClass = Common::GET('app');
                    $app = Framework::loadApp($appClass);
                } else
                    $app = FALSE;


                if (!$app) {
                    $appClass = Framework::getMainApp();
                    $app = Framework::loadApp($appClass);

                    Language::setLang($appClass);

                    $controllerClass = $app->getDefaultController();
                    $controller = $app->loadController($controllerClass);

                    $action = $controller->getDefaultAction();
                    $controller->$action();
                } else {

                    Language::setLang($appClass);

                    if (array_key_exists('controller', $_GET)) {
                        $controllerClass = $_GET['controller'];
                        $controller = $app->loadController($controllerClass);
                    } else
                        $controller = FALSE;

                    if (!$controller) {
                        $controllerClass = $app->getDefaultController();
                        $controller = $app->loadController($controllerClass);

                        $action = $controller->getDefaultAction();
                        $controller->$action();
                    } else {

                        $action = Common::GET('action');
                        if ($action && method_exists($controller, $action)) {

                            $param = Controller::getParam(Common::GET('param'));
                            $controller->$action($param);
                        } else {

                            $action = $controller->getDefaultAction();
                            $controller->$action();
                        }
                    }
                }
            } else {
                $appClass = Common::GET('app');
                $controllerClass = Common::GET('controller');
                if (($appClass == "init") && ($controllerClass == "gui")) {
                    // user has expired the session and is trying to reload the gui - refresh or F5
                    // redirect to login
                    header('Location: index.php?message=Session Expired');
                } else
                    header('HTTP/1.1 402 Timeout');
            }
        }
    }

}
