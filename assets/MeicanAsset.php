<?php

namespace app\assets;

use yii\web\AssetBundle;

class MeicanAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/style.css',
    	'css/pagination.css',
    	'css/notification.css',
    	'css/feedback.css',
    ];
    
    public $js = [
    	'js/main.js',
    	'js/init/feedback-i18n.js',
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset',
        'app\assets\MeicanJuiAsset',
        'app\assets\MeicanI18NAsset',
    ];
}
