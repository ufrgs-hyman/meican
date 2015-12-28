<?php

namespace meican\topology\assets\service;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/public';

    public $js = [
        'service/service.js',
    ];
}
