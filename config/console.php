<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'meican-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@meican' => '@app',
    ],
    'modules' => [
    	'aaa' => 'meican\modules\aaa\AaaModule',
    	'circuits' => 'meican\modules\circuits\CircuitsModule',
    	'init' => 'meican\modules\init\InitModule',
    	'topology' => 'meican\modules\topology\TopologyModule',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'trace'],
                    'logFile' => dirname(__DIR__).'/runtime/logs/console.log',
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];
