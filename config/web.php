<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'meican',
    'name'=>'MEICAN',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'debug','session'],
    'defaultRoute' => 'init',
    'modules' => [
	    'debug' => [
	    	'class' => 'yii\debug\Module',
	    	//'allowedIPs' => ['143.54.12.245']
	    ],
	    'aaa' => 'meican\modules\aaa\AaaModule',
		'circuits' => 'meican\modules\circuits\CircuitsModule',
		'init' => 'meican\modules\init\InitModule',
		'topology' => 'meican\modules\topology\TopologyModule',
		'bpm' => 'meican\modules\bpm\BpmModule',
    	'notification' => 'meican\modules\notification\NotificationModule',
		'gii' => 'yii\gii\Module',
	],
    'aliases' => [
        '@meican' => '@app',
    ],
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [ 
                'yii\jui\JuiAsset' => [
                    'css' => [],
                    'js' => [],
                    'depends' => [
                        'meican\assets\MeicanJuiAsset',
                    ]
                ]
            ],
        ],
    	'urlManager' => [
	    	'class' => 'yii\web\UrlManager',
	    	'enablePrettyUrl' => true,
	    	'showScriptName' => false,
    	],
    	'session' => [
	    	'class' => 'yii\web\Session',
	    	'cookieParams' => ['httpOnly' => true, 'lifetime'=> 3600],
	    	'timeout' => 3600,
	    	'useCookies' => true,
    	],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'asdadasdas',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'meican\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['init/login']
        ],
        'errorHandler' => [
            'errorAction' => 'init/default/error',
        ],
        'mailer' => require(__DIR__ . '/mailer.php'),
        'log' => [
            'flushInterval' => 1000, 
            'traceLevel' => YII_DEBUG ? 1 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace'],
                    'exportInterval' => 1000, 
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'authManager' => [
	        'class' => 'yii\rbac\DbManager',
	        'defaultRoles' => ['guest'],
        ],
        'i18n' => [
        	'translations' => [
	        	'aaa*' => [
		        	'class' => 'yii\i18n\PhpMessageSource',
		        	'basePath' => '@app/messages',
			        'sourceLanguage' => 'en-US',
			        'fileMap' => [
				        'aaa' => 'aaa.php',
			        ],
				],
		        'init*' => [
			        'class' => 'yii\i18n\PhpMessageSource',
			        'basePath' => '@app/messages',
			        'sourceLanguage' => 'en-US',
			        'fileMap' => [
				        'init' => 'init.php',
			        ],
		        ],
		        'bpm*' => [
			        'class' => 'yii\i18n\PhpMessageSource',
			        'basePath' => '@app/messages',
			        'sourceLanguage' => 'en-US',
			        'fileMap' => [
			        	'bpm' => 'bpm.php',
		        	],
		        ],
		        'circuits*' => [
			        'class' => 'yii\i18n\PhpMessageSource',
			        'basePath' => '@app/messages',
			        'sourceLanguage' => 'en-US',
			        'fileMap' => [
			        	'circuits' => 'circuits.php',
			        ],
		        ],
		        'topology*' => [
			        'class' => 'yii\i18n\PhpMessageSource',
			        'basePath' => '@app/messages',
			        'sourceLanguage' => 'en-US',
			        'fileMap' => [
			        	'topology' => 'topology.php',
			        ],
		        ],
        		'notification*' => [
        			'class' => 'yii\i18n\PhpMessageSource',
        			'basePath' => '@app/messages',
        			'sourceLanguage' => 'en-US',
        			'fileMap' => [
        				'notification' => 'notification.php',
        			],
        		],
	        ],
        ],
    ],
    'params' => $params,
];

return $config;
