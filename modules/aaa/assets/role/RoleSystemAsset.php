<?php

namespace meican\aaa\assets\role;

use yii\web\AssetBundle;

class RoleSystemAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/role/public';

    public $js = [
    	'roleSystem.js',
    ];
    
    public $depends = [
        'meican\base\assets\Theme',
    ];
}
