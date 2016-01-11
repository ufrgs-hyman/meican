<?php

namespace meican\assets;

use yii\web\AssetBundle;
use Yii;

class LeafletAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $css = [
        '@npm/leaflet/dist/leaflet.css',
    ];
    
    public $js = [
        '@npm/leaflet/dist/leaflet.js',
    ];
}

?>