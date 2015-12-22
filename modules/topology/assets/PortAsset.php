<?php

namespace meican\modules\topology\assets;

use yii\web\AssetBundle;

class PortAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
    	'js/topology/port.js',
    	'js/topology/port-i18n.js',
    ];
    public $depends = [
		'yii\web\JqueryAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
