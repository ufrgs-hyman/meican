<?php

namespace app\modules\topology\assets;

use yii\web\AssetBundle;

class FormDeviceAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
    	'js/topology/addDevice.js',
    ];
    public $depends = [
		'yii\web\JqueryAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
