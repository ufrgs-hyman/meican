<?php

namespace meican\base\assets;

use yii\web\AssetBundle;

class IcheckAsset extends AssetBundle
{
    public $sourcePath = '@bower/icheck';
    
    public $js = [
        'icheck.min.js'
    ];
    
    public $css = [
        'skins/minimal/blue.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
