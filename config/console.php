<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'meican-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
    	'aaa' => 'app\modules\aaa\AaaModule',
    	'circuits' => 'app\modules\circuits\CircuitsModule',
    	'init' => 'app\modules\init\InitModule',
    	'topology' => 'app\modules\topology\TopologyModule',
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
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];
