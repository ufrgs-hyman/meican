<?php

namespace meican\aaa\assets\role;

use yii\web\AssetBundle;

class CreateEditAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/role/public';

    public $js = [
    	'roleCreateEdit.js',
    ];
    
    public $depends = [
		'meican\base\assets\layout\Asset',
    ];
}
