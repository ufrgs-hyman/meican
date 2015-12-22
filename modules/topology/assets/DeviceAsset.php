<?php

namespace meican\modules\topology\assets;

use yii\web\AssetBundle;

class DeviceAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
    	'js/topology/device.js',
    ];
    public $depends = [
    		'yii\web\JqueryAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
