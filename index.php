<?php

$init_session = true;

include_once 'meican.conf.php';
include_once 'includes/auth.inc';
include_once 'includes/language.inc';
include_once 'includes/common.inc';
include_once 'libs/database.inc';
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
