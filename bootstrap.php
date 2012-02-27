<?php

function __d($domain, $msg, $args = null) {
	if (!$msg) {
		return;
	}
	return vsprintf($msg, $args);
}

//require CAKE . 'basics.php';
/*
if (!defined('WEBROOT_DIR')) {
    define('WEBROOT_DIR', 'webroot');
}
if (!defined('WWW_ROOT')) {
    define('WWW_ROOT', dirname(__FILE__) . DS . 'webroot');
}*/

require LIBS . 'Core' . DS .'App.php';
require LIBS . 'Error' . DS . 'exceptions.php';

spl_autoload_register(array('App', 'load'));

App::uses('ErrorHandler', 'Error');
App::uses('Configure', 'Core');
App::uses('CakePlugin', 'Core');
App::uses('Cache', 'Cache');
App::uses('Object', 'Core');
App::uses('Log', 'Log');
//App::$bootstrapping = true;

//include_once 'libs/Core/Configure.php';
//include_once 'libs/Log/Log.php';
Log::config('default', array(
    'engine' => 'FileLog'
));
//include_once 'libs/Error/ErrorHandler.php';
Configure::bootstrap(true);
Configure::load('config/main.php');
Configure::load('config/local.php');

include_once 'libs/common.php';

$engine = 'File';
if (extension_loaded('apc') && function_exists('apc_dec') && (php_sapi_name() !== 'cli' || ini_get('apc.enable_cli'))) {
	$engine = 'Apc';
}

// In development mode, caches should expire quickly.
$duration = '+999 days';
if (Configure::read('debug') >= 1) {
	$duration = '+10 seconds';
}

/**
 * Configure the cache used for general framework caching.  Path information,
 * object listings, and translation cache files are stored with this configuration.
 */
Cache::config('default', array(
	'engine' => $engine,
	'prefix' => 'default',
	'path' => 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));
