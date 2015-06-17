<?php

namespace app\modules\circuits\assets;

use yii\web\AssetBundle;

class AutomatedTestAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
    	'js/circuits/tests/tests-i18n.js',
    	'js/circuits/tests/tests.js',
    	'js/jquery.timepicker.min.js',
    ];
    
    public $css = [
		'css/jquery.timepicker.css',
    ];
    
    public $depends = [
		'yii\web\JqueryAsset',
    ];
}
