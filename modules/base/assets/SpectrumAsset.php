<?php

namespace meican\assets;

use yii\web\AssetBundle;

class SpectrumAsset extends AssetBundle
{
    public $sourcePath = '@bower/spectrum';
    
    public $js = [
        'spectrum.js',
    ];
    
    public $css = [
        'spectrum.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
