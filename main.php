<?php

include_once 'framework.conf.php';
include_once 'includes/auth.inc';
include_once 'includes/language.inc';
include_once 'includes/common.inc';
include_once 'libs/database.inc';

defined ('__FRAMEWORK') or die ("Invalid access.");

Framework::initWebRoot();

if (array_key_exists('app', $_GET) && array_key_exists('services', $_GET)) {
       $appClass = Common::GET('app');
       $app = Framework::loadApp($appClass);
       $controller = $app->loadController('ws');
} else {

    if (AuthSystem::userTryToLogin() || AuthSystem::isUserLoggedIn()) {

        if (array_key_exists('app', $_GET)) {
            $appClass = Common::GET('app');
            $app = Framework::loadApp($appClass);
        } else $app = FALSE;


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
                    if ($action && method_exists($controller, $action)){

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
                
            } else header('HTTP/1.1 402 Timeout');
        }

    }

?>