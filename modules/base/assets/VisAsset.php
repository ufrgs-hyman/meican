<?php

namespace meican\base\assets;

use yii\web\AssetBundle;

class VisAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        '@npm/vis/dist/vis.min.js',
    ];
    
    public $css = [
        '@npm/vis/dist/vis.min.css',
    ];
    
    public $depends = [
    ];
}
