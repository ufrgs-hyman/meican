<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define("LIBS", 'libs'.DS);
define('APP', ROOT . DS);
define('APP_DIR', DS);
//define("LOGS", ROOT.DS.'log'.DS);
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', WEBROOT_DIR . DS);
@define ('__MEICAN', 1);

require 'bootstrap.php';

//defined('__MEICAN') or die("Invalid access.");

session_start();
include_once 'libs/Core/Dispatcher.php';

Dispatcher::getInstance()->dispatch(new CakeRequest(), new CakeResponse());
