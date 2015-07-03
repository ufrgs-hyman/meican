<?php

namespace app\assets;

use yii\web\AssetBundle;

class MeicanAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    	'css/jquery-ui.min.css',
        'css/jquery-ui.structure.min.css',
        'css/jquery-ui.theme.min.css',
        'css/style.css',
    	'css/notification.css',
    ];
    public $js = [
    	'js/jquery-ui.min.js',
    	'js/main.js',
    	'js/init/feedback-i18n.js',
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset',
    ];
}
