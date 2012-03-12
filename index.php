<?php

/*
define('APP_DIR', '');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);*/

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define("LIBS", 'libs'.DS);
define("APPLIBS", 'libs'.DS);
define("CAKE", LIBS);
define('APP', DS);
define('APP_DIR', DS);
define("LOGS", ROOT.DS.'log'.DS);
@define ('__MEICAN', 1);

require 'bootstrap.php';

//defined('__MEICAN') or die("Invalid access.");

session_start();
App::uses('Dispatcher', 'Core');//include_once 'libs/Core/Dispatcher.php';
Dispatcher::getInstance()->dispatch();
