<?php

namespace meican\base\assets;

use yii\web\AssetBundle;

class MeicanGraphAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        'js/graph/meican-graph.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
        'meican\assets\MeicanI18NAsset',
        'meican\assets\VisAsset',
        'meican\assets\QtipAsset'
    ];
}
