<?php

namespace meican\topology\assets\service;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/service/public';

    public $js = [
        'service.js',
    ];

    public $depends = [
		'meican\base\assets\Theme',
    ];
}
