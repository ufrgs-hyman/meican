<?php

namespace meican\aaa\assets\role;

use yii\web\AssetBundle;

class RoleDomainAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/role/public';

    public $js = [
    	'roleDomain.js',
    ];
    
    public $depends = [
        'meican\base\assets\Theme',
    ];
}
