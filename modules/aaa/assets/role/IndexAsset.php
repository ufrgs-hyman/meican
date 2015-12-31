<?php

namespace meican\aaa\assets\role;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/role/public';

    public $js = [
    	'role.js',
    ];
    
    public $depends = [
        'meican\base\assets\layout\Asset',
    ];
}
