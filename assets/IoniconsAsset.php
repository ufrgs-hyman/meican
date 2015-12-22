<?php

namespace meican\assets;

use yii\web\AssetBundle;

class IoniconsAsset extends AssetBundle
{
    public $sourcePath = '@bower/ionicons';
    
    public $js = [
    ];
    
    public $css = [
        'css/ionicons.min.css',
    ];
    
    public $depends = [
    ];
}
