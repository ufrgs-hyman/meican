#! /usr/bin/php

<?php
    echo date("Y M j - H:i:s");
    $init_session = false;
    include_once 'framework.conf.php';
    include_once 'libs/database.inc';

    $appClass = $argv[1];
    $controllerClass = $argv[2];
    $action = $argv[3];

    $app = Framework::loadApp($appClass);
    $controller = $app->loadController($controllerClass);
    $controller->$action();


?>
