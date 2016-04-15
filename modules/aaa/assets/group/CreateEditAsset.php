<?php

namespace meican\aaa\assets\group;

use yii\web\AssetBundle;

class CreateEditAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/group/public';

    public $js = [
        'groupCreateEdit.js',
    ];
    
    public $depends = [
        'meican\base\assets\Theme',
    ];
}
