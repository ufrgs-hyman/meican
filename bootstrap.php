<?php
/* bootstrap CAKE PHP */

/**
 * Path to the temporary files directory.
 */
if (!defined('TMP')) {
	define('TMP', APP . DS);
}
$boot = false;
if (!include (LIBS . 'Cake' . DS . 'bootstrap.php')) {
    $failed = true;
}
if (!empty($failed)) {
	trigger_error("CakePHP core could not be found.  Check the value of CAKE_CORE_INCLUDE_PATH in APP/webroot/index.php.  It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
}

include_once 'libs/common.php';
App::uses('CakeLog', 'Log');
CakeLog::config('default', array(
    'engine' => 'FileLog'
));

//App::uses('PhpReader', 'Configure');
include_once 'libs/IncludeReader.php';
Configure::config('default', new IncludeReader(APP . 'config' . DS));
Configure::write('App', array(
				'base' => false,
				'baseUrl' => false,
				'dir' => APP_DIR,
				'webroot' => WEBROOT_DIR,
				'www_root' => WWW_ROOT
			));
Configure::load('main.php');
Configure::load('local.php');
//Configure::bootstrap(true);