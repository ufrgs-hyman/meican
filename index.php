<?php

$init_session = true;

include_once 'meican.conf.php';
include_once 'libs/auth.php';
include_once 'libs/language.php';
include_once 'libs/common.php';
include_once 'libs/database.php';
include_once 'libs/dispatcher.php';


defined('__MEICAN') or die("Invalid access.");


Framework::initWebRoot();


$mdb2 = MDB2::singleton(Framework::getDatabaseString());
if (MDB2::isError($mdb2)) {
    Framework::debug($mdb2->getMessage() . ", " . $mdb2->getDebugInfo());
    die($mdb2->getMessage());
}
Dispatcher::getInstance()->dispatch();
$mdb2->disconnect();

?>
