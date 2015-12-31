<?php

namespace meican\base\assets\layout;

use yii\web\AssetBundle;

class IcheckConfigAsset extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/layout/public';

    public $js = [
        'icheck-config.js',
    ];
    
    public $depends = [
        'meican\base\assets\IcheckAsset'
    ];
}
