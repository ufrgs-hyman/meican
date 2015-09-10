<?php

namespace app\assets;

use yii\web\AssetBundle;

class MeicanI18NAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        'js/i18n/meican-i18n.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
    ];
}
