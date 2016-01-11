<?php

namespace meican\modules\topology\assets;

use yii\web\AssetBundle;

class GraphAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/topology/viewer/graph.js',
    ];

    public $css = [
        'css/topology/viewer/graph.css',
    ];

    public $depends = [
        'meican\assets\MeicanAsset',
        'meican\assets\VisAsset',
    ];
}
