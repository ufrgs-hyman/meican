<?php

namespace meican\assets\layout;

use yii\web\AssetBundle;

class LayoutAsset extends AssetBundle
{
    public $sourcePath = '@meican/assets/layout/public';

    public $css = [
        'layout.css',
        'theme.css'
    ];
    
    public $js = [
        'layout.js'
    ];
    
    public $depends = [
        'meican\assets\BootstrapAsset',
        'meican\assets\SlimScrollAsset',
        'meican\assets\FontAwesomeAsset',
        'meican\assets\IoniconsAsset',
        'meican\assets\IcheckAsset'
    ];
}
