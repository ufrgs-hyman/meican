<?php

namespace meican\assets;

use yii\web\AssetBundle;

class TimePickerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        'js/jquery.timepicker.min.js',
    ];
    
    public $css = [
        'css/jquery.timepicker.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
