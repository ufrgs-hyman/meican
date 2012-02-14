#! /usr/bin/php

<?php

define("DS", '/');
define("LIBS", 'libs'.DS);
define('ROOT', dirname(__FILE__));
define("LOGS", ROOT.DS.'log'.DS);

echo date("Y M j - H:i:s");
$init_session = false;
require 'bootstrap.php';
include_once 'libs/Core/Dispatcher.php';

Dispatcher::getInstance()->dispatch(array(
    'app' => $argv[1],
    'controller' => $argv[2],
    'action' => $argv[3]
));