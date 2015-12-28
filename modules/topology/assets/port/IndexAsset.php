<?php

namespace meican\topology\assets\port;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/public';

    public $js = [
    	'port/port.js',
    	'port/port-i18n.js',
    ];
    public $depends = [
		'yii\web\JqueryAsset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
