<?php

$init_session = true;


define('APP_DIR', '');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);

define("LIBS", 'libs'.DS);
define("APPLIBS", 'libs'.DS);
define("CAKE", LIBS);
define('APP', '/');
define("LOGS", ROOT.DS.'log'.DS);
@define ('__MEICAN', 1);

require 'bootstrap.php';
include_once 'libs/Core/Dispatcher.php';


//defined('__MEICAN') or die("Invalid access.");

session_start();
Dispatcher::getInstance()->dispatch();
