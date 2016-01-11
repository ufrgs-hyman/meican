<?php

namespace meican\base\assets;

use yii\web\AssetBundle;

class QtipAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        'js/vendor/jquery.qtip.min.js',
    ];
    
    public $css = [
        'css/vendor/jquery.qtip.min.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
