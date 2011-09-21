<?php

$init_session = true;

include_once 'meican.conf.php';
include_once 'includes/auth.inc';
include_once 'includes/language.inc';
include_once 'includes/common.inc';
include_once 'libs/database.inc';
include_once 'libs/router.php';
include_once 'libs/dispatcher.php';

defined('__MEICAN') or die("Invalid access.");

Framework::initWebRoot();
$dispatcher = new Dispatcher();
$dispatcher->dispatch();



?>