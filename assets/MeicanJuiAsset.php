<?php

namespace app\assets;

use yii\web\AssetBundle;

class MeicanJuiAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/jquery-ui.min.css',
        'css/jquery-ui.structure.min.css',
        'css/jquery-ui.theme.min.css',
    ];
    
    public $js = [
        'js/jquery-ui.min.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
