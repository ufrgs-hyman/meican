<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'meican-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@meican' => '@app/modules',
    ],
    'modules' => [
        'aaa' => 'meican\aaa\Module',
        'circuits' => 'meican\circuits\Module',
        'scheduler' => 'meican\scheduler\Module',
        'topology' => 'meican\topology\Module',
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
