#! /usr/bin/php

<?php

echo date("Y M j - H:i:s");
$init_session = false;
include_once 'meican.conf.php';
include_once 'libs/database.inc';

$mdb2 = MDB2::singleton(Framework::getDatabaseString());
if (MDB2::isError($mdb2)) {
    Framework::debug($mdb2->getMessage() . ", " . $mdb2->getDebugInfo());
    die($mdb2->getMessage());
}

$appClass = $argv[1];
$controllerClass = $argv[2];
$action = $argv[3];

$app = Framework::loadApp($appClass);
$controller = $app->loadController($controllerClass);
$controller->$action();

$mdb2->disconnect();

?>