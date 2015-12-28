<?php

namespace meican\base\assets;

use yii\web\AssetBundle;

class HoverIntentAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-hoverintent';
    
    public $js = [
    	'jquery.hoverIntent.js'
    ];
    
    public $css = [
    ];
    
    public $depends = [
    ];
}
