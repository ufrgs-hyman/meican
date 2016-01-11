<?php

namespace meican\base\assets;

use yii\web\AssetBundle;

class MeicanLeafletAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        'js/maps/meican-leaflet-map.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
        'meican\assets\LeafletAsset',
        'meican\assets\MeicanI18NAsset',
    ];
}
