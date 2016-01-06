<?php

namespace meican\aaa\assets\user;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/user/public';
    
    public $js = [
        'index.js',
    ];
    
    public $depends = [
        'meican\base\assets\layout\Asset',
    ];
}
