<?php

namespace meican\assets;

use yii\web\AssetBundle;

class TimePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/timepicker';
    
    public $js = [
        'jquery.timepicker.min.js',
    ];
    
    public $css = [
        'jquery.timepicker.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
