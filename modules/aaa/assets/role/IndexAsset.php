<?php

namespace meican\aaa\assets\role;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/public';

    public $js = [
    	'role/role.js',
    ];
    
    public $css = [ 
    ];

    public $depends = [
		'yii\web\JqueryAsset',
    ];
}
