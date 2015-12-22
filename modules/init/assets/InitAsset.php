<?php

namespace meican\modules\init\assets;

use yii\web\AssetBundle;

class InitAsset extends AssetBundle
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
        'meican\assets\FontAwesomeAsset',
        'meican\assets\IoniconsAsset',
        'meican\assets\IcheckAsset'
    ];
}
