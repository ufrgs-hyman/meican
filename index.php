<?php

$init_session = false;
include_once 'meican.conf.php';

defined('__MEICAN') or die("Invalid access.");

include_once 'apps/init/controllers/login.php';
include_once 'includes/language.inc';
include_once 'libs/dispatcher.php';

Language::setLang('init');

$login = new Login();

if (key_exists('message', $_GET))
    $message = $_GET['message'];
else
    $message = NULL;

$login->show($message);

?>
