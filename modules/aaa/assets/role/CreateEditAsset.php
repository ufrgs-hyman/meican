<?php

namespace meican\aaa\assets\role;

use yii\web\AssetBundle;

class CreateEditAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/public';

    public $js = [
    	'role/roleCreateEdit.js',
    ];
    
    public $depends = [
		'yii\web\JqueryAsset',
    ];
}
