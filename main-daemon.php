#! /usr/bin/php

<?php

echo date("Y M j - H:i:s");
$init_session = false;
include_once 'meican.conf.php';
include_once 'libs/dispatcher.php';

Dispatcher::getInstance()->dispatch(array(
    'app' => $argv[1],
    'controller' => $argv[2],
    'action' => $argv[3]
));