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
  } */

require LIBS . 'Core' . DS . 'App.php';
require LIBS . 'Error' . DS . 'exceptions.php';

spl_autoload_register(array('App', 'load'));

App::uses('ErrorHandler', 'Error');
App::uses('Configure', 'Core');
App::uses('CakePlugin', 'Core');
App::uses('Cache', 'Cache');
App::uses('Object', 'Core');
App::uses('Log', 'Log');
App::uses('Inflector', 'Utility');

include_once 'libs/common.php';
//App::$bootstrapping = true;
//include_once 'libs/Core/Configure.php';
//include_once 'libs/Log/Log.php';
Log::config('default', array(
    'engine' => 'FileLog'
));
//include_once 'libs/Error/ErrorHandler.php';
Configure::load('config/main.php');
Configure::load('config/local.php');
Configure::bootstrap(true);