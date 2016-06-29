<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'meican',
    'name'=>'MEICAN - Management Environment of Inter-domain Circuits for Advanced Networks',
    'version' => '3.0.0',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','debug','session',
        'notify',
        'home',
        'bpm',
        'circuits',
        'topology',
        'aaa',
        'tester',
        'monitoring',
    ],
    'defaultRoute' => 'home',
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['143.54.12.*']
        ],
        'aaa' =>            'meican\aaa\Module',
        'base' =>           'meican\base\Module',
        'circuits' =>       'meican\circuits\Module',
        'home' =>           'meican\home\Module',
        'scheduler' =>      'meican\scheduler\Module',
        'tester' =>         'meican\tester\Module',
        'monitoring' =>     'meican\monitoring\Module',
        'topology' =>       'meican\topology\Module',
        'bpm' =>            'meican\bpm\Module',
        'notify' =>         'meican\notify\Module',
        'oscars' =>         'meican\oscars\Module',
        'gii' =>            'yii\gii\Module',
    ],
    'aliases' => [
        '@meican' => '@app/modules',
    ],
    'components' => [
        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'session' => [
            'class' => 'yii\web\Session',
            //'cookieParams' => ['httpOnly' => true, 'lifetime'=> 3600],
            //'timeout' => 3600,
            //'useCookies' => true,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'sadasddsad',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'meican\aaa\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['aaa/login']
        ],
        'errorHandler' => [
            'errorAction' => 'home/board/error',
        ],
        'mailer' => require(__DIR__ . '/mailer.php'),
        'log' => [
            'traceLevel' => YII_DEBUG ? 1 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace'],
                    'logFile' => dirname(__DIR__).'/runtime/logs/web.log',
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'],
        ],
    ],
    'params' => $params,
];

return $config;
