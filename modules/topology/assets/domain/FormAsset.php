<?php

namespace meican\topology\assets\domain;

use yii\web\AssetBundle;

class FormAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/public';

    public $js = [
        'domain/form.js',
    ];

    public $css = [
        'domain/form.css',
    ];

    public $depends = [
        'meican\base\assets\SpectrumAsset',
    ];
}
