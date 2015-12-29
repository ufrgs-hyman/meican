<?php

namespace meican\topology\assets\domain;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/domain/public';

    public $js = [
        'index.js',
    ];

    public $depends = [
    	'meican\base\assets\layout\Asset',
    ];
}
