<?php

namespace app\assets;

use yii\web\AssetBundle;

class SpectrumAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        'js/vendor/spectrum.js',
    ];
    
    public $css = [
        'css/vendor/spectrum.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
