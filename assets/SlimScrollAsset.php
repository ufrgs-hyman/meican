<?php

namespace meican\assets;

use yii\web\AssetBundle;

class SlimScrollAsset extends AssetBundle
{
    public $sourcePath = '@bower/slimscroll';
    
    public $js = [
        'jquery.slimscroll.min.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
