<?php

namespace meican\base\assets;

use yii\web\AssetBundle;
use Yii;

class ToggleAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-toggle';
    
    public $js = [
        'js/bootstrap-toggle.min.js',
    ];
    
    public $css = [
    	'css/bootstrap-toggle.min.css',
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}

?>