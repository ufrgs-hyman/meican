<?php

$init_session = true;

define("DS", '/');
define("LIBS", 'libs'.DS);
define('ROOT', dirname(__FILE__));
define("LOGS", ROOT.DS.'log'.DS);
@define ('__MEICAN', 1);

include_once 'libs/common.php';
include_once 'libs/dispatcher.php';


//defined('__MEICAN') or die("Invalid access.");

session_start();
Dispatcher::getInstance()->dispatch();
