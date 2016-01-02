<?php

namespace meican\base\assets\grid;

use yii\web\AssetBundle;

class ButtonsAsset extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/grid/public';

    public $js = [
        'grid-buttons.js',
    ];
    
    public $depends = [
        'meican\base\assets\layout\Asset',
    ];
}
