<?php

namespace app\assets;

use yii\web\AssetBundle;

class CronPickerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        'js/vendor/jquery-cron-i18n.js',
        'js/vendor/jquery-cron.js',
    ];
    
    public $css = [
        'css/circuits/jquery-cron.css',
    ];
    
    public $depends = [
        'app\assets\MeicanI18NAsset',
        'yii\web\JqueryAsset',
    ];
}
