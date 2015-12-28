<?php

namespace meican\aaa\assets\group;

use yii\web\AssetBundle;

class CreateEditAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/public';

    public $js = [
    	'group/groupCreateEdit.js',
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset',
    ];
}
