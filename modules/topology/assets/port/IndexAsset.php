<?php

namespace meican\topology\assets\port;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/port/public';

    public $js = [
    	'port.js',
    	'port-i18n.js',
    ];
    
    public $depends = [
		'meican\base\assets\layout\Asset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
