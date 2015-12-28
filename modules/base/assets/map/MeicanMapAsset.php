<?php

namespace meican\base\assets\map;

use yii\web\AssetBundle;

class MeicanMapAsset extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/map/public';
    
    public $js = [
        'meican-map.js',
        'meican-map-i18n.js'
    ];
    
    public $css = [
    ];
    
    public $depends = [
        'meican\base\assets\google\GoogleMapsAsset',
        'meican\base\assets\i18n\MeicanI18NAsset',
    ];
}
