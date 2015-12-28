<?php

namespace meican\base\assets;

use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@bower/fontawesome';
    
    public $js = [
    ];
    
    public $css = [
        'css/font-awesome.min.css'
    ];
    
    public $depends = [
    ];
}
