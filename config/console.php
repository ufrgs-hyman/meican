<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'meican-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log',
        'notify',
        'home',
        'bpm',
        'circuits',
        'topology',
        'aaa',
        'tester',
        'monitoring'
    ],
    'aliases' => [
        '@meican' => '@app/modules',
    ],
    'modules' => [
        'aaa' =>        'meican\aaa\Module',
        'circuits' =>   'meican\circuits\Module',
        'scheduler' =>  'meican\scheduler\Module',
        'oscars'    =>  'meican\oscars\Module',
        'nsi'       =>  'meican\nsi\Module',
        'base' =>           'meican\base\Module',
        'home' =>           'meican\home\Module',
        'tester' =>         'meican\tester\Module',
        'monitoring' =>     'meican\monitoring\Module',
        'topology' =>       'meican\topology\Module',
        'bpm' =>            'meican\bpm\Module',
        'notify' =>         'meican\notify\Module',
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
                     'logVars' => [],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];
