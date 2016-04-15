<?php

namespace meican\circuits\assets\config;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/config/public';
    
    public $js = [
        'config.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
