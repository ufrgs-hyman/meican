<?php

namespace app\modules\topology\assets\domain;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/topology/domain/index.js',
    ];

    public $depends = [
        'app\assets\SpectrumAsset',
    ];
}
