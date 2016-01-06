<?php

namespace meican\aaa\assets\login;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/login/public';
    
    public $js = [
        'login.js',
    ];
    
    public $depends = [
        'meican\base\assets\layout\Asset',
    ];
}
