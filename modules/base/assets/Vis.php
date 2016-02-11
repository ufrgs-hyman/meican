<?php

namespace meican\base\assets;

use yii\web\AssetBundle;

class Vis extends AssetBundle
{
    public $sourcePath = '@npm/vis/dist';
    
    public $js = [
        'vis.min.js',
    ];
    
    public $css = [
        'vis.min.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
