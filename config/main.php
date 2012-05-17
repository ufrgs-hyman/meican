<?php

return array(
    'Error' => array(
        'handler' => 'ErrorHandler::handleError',
        'level' => E_ALL  & ~E_DEPRECATED,
        'trace' => true
    ),
    'Exception' => array(
        'handler' => 'ErrorHandler::handleException',
        'renderer' => 'ExceptionRenderer',
        'log' => true
    ),
    'Asset' => array(
        //'compress' => true,
        'filter' => array(
            'css' => 'assets.php',
            'js' => 'assets.php'
        )
    ),
    'apps' => array('aaa', 'bpm', 'init', 'topology', 'circuits'),
    /* internal system variables */
    "documentRoot" => null,
    "dirSeparator" => null,
    "webRoot" => null,
    "systemTimeout" => 3600,
    "cookieLifetime" => 2592000,
    /* log settings */
    "tmpFolder" => null,
    "logFolder" => null,
    "systemLogFolder" => null,
    /* system profile */
    "systemName" => 'MEICAN - Management Environment of Inter-domain Circuits for Advanced Networks',
    "systemNameShort" => 'MEICAN',
    "systemVer" => '',
    "mainApp" => 'circuits',
    "systemDirName" => 'meican/',
    /* database settings */
    "defaultDatabase" => 'mysql',
    "dbConfig" => null,
    "defaultLang" => 'pt_BR.utf8',
    "debug" => 2,
    "useACL" => false,

    /**
     * OSCARS Bridge configuration -> it must refers to the WSDL
     */
    "OSCARSBridgeEPR" => 'http://localhost:8080/axis2/services/OSCARSBridge?wsdl',
    
    'MapsAPIKey' => "",
    
    'databases' => array(
        'default' => array(
            'datasource' => 'Database/Mysql',
            'persistent' => false,
            'host' => 'localhost',
            'login' => 'root',
            'password' => 'futurarnp',
            'database' => 'meican',
            'prefix' => '',
        //'encoding' => 'utf8',
        )
    ),
    'noQueryCahe' => false
);
