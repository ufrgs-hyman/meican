<?php

namespace meican\modules\topology\assets\domain;

use yii\web\AssetBundle;

class FormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/topology/domain/form.js',
    ];

    public $css = [
        'css/topology/domain/form.css',
    ];

    public $depends = [
        'meican\assets\SpectrumAsset',
    ];
}
