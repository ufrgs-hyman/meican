<?php

namespace meican\assets;

use yii\web\AssetBundle;

class LayoutAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/layout/layout.css',
        'css/layout/skin-blue-light.css'
    ];
    
    public $js = [
        'js/layout/layout.js'
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'meican\assets\SlimScrollAsset',
        'meican\assets\FontAwesomeAsset',
        'meican\assets\IoniconsAsset'
    ];
}
