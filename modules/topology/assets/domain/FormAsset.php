<?php

namespace meican\topology\assets\domain;

use yii\web\AssetBundle;

class FormAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/domain/public';

    public $js = [
        'form.js',
    ];

    public $css = [
        'form.css',
    ];

    public $depends = [
        'meican\base\assets\layout\Asset',
        'meican\base\assets\SpectrumAsset',
    ];
}
