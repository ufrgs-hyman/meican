<?php

$init_session = true;

define("DS", '/');
define("LIBS", 'libs'.DS);
define('ROOT', dirname(__FILE__));

include_once 'meican.conf.php';
include_once 'libs/common.php';
include_once 'libs/dispatcher.php';


defined('__MEICAN') or die("Invalid access.");

session_start();
Dispatcher::getInstance()->dispatch();
