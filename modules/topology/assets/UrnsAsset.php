<?php

namespace app\modules\topology\assets;

use yii\web\AssetBundle;

class UrnsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [

    ];
    public $js = [
    	'js/topology/urn.js',
    	'js/topology/urn-i18n.js',
    ];
    public $depends = [
		'yii\web\JqueryAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
