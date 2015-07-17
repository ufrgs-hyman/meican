<?php

namespace app\modules\circuits\assets;

use yii\web\AssetBundle;

class ConfigurationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        'js/circuits/config.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
