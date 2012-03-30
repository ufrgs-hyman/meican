#! /usr/bin/php

<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define("LIBS", 'libs'.DS);
define("APPLIBS", 'libs'.DS);
define("CAKE", LIBS);
define('APP', '');
define('APP_DIR', DS);
define("LOGS", ROOT.DS.'log'.DS);
@define ('__MEICAN', 1);

echo date("Y M j - H:i:s");
$init_session = false;
define('NO_SESSION', true);
require 'bootstrap.php';
include_once 'libs/Core/Dispatcher.php';

Dispatcher::getInstance()->dispatch(array(
    'app' => $argv[1],
    'controller' => $argv[2],
    'action' => $argv[3],
    'verify_login' => false
));