<?php

return array(
    
	'Error' => array(
		'handler' => 'ErrorHandler::handleError',
		'level' => E_ALL /*| E_DEPRECATED*/ ,
		'trace' => true
	),
    
    'Exception' => array(
		'handler' => 'ErrorHandler::handleException',
		'renderer' => 'ExceptionRenderer',
		'log' => true
	),
    
    'Asset' => array(
<<<<<<< HEAD
        //'compress' => true,
=======
        'compress' => true,
>>>>>>> e0f7ee6560f12cfdfefd7bc8dab41b1a9e6ec26e
        'filter' => array(
            'css' => 'assets.php',
            'js' => 'assets.php'
        )
        
    ),
    
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
    "mainApp" => 'aaa',
    "systemDirName" => 'meican/',
    /* database settings */
    "defaultDatabase" => 'mysql',
    "dbConfig" => null,
    "defaultLang" => 'pt_BR.utf8',
    "debug" => 0,
    "useACL" => false,
    /**
     * CUIDADO COM O ENDEREÇO PARA OS WEBSERVICES
     * ALTERAR DE ACORDO COM A PORTA FORNECIDA PELA NOC
     */
    "fedIp" => 'noc.inf.ufrgs.br:65501', // route to 143.54.12.123:80
    "odeIp" => 'noc.inf.ufrgs.br:65401', // route to 143.54.12.123:8080

    /**
     *
     * ODE CONFIGURACOES
     * @var $odeWSDLToRequest: reservation_info > sendForAuthorization
     * @var $odeWSDLToResponse: request_info > response
     */
    "odeWSDLToRequest" => "http://noc.inf.ufrgs.br:65401/ode/deployment/bundles/v4_felipe_workflow/processes/v4_felipe_workflow/processes.ode/diagrama-ODE_Workflow_Felipe.wsdl",
    "odeWSDLToResponse" => "http://noc.inf.ufrgs.br:65401/ode/deployment/bundles/v4_felipe_workflow/processes/v4_felipe_workflow/processes.ode/diagrama-ODE_Workflow_Felipe.wsdl",
    /**
     * NÃO ALTERAR
     */
    "OSCARSBridgeEPR" => 'http://localhost:8080/axis2/services/OSCARSBridge?wsdl',
    /*
     * 
     * an associative array with the following keys:
      phptype: Database backend used in PHP (mysql, odbc etc.)
      dbsyntax: Database used with regards to SQL syntax etc.
      protocol: Communication protocol to use (tcp, unix etc.)
      hostspec: Host specification (hostname[:port])
      database: Database to use on the DBMS server
      username: User name for login
      password: Password for login
     */
    'database' => array(
        'mysql' => array(
            'phptype' => 'mysql',
            'username' => 'root',
            'password' => 'futurarnp',
            'hostspec' => 'localhost',
            'database' => 'meican'
        )
    )
);
