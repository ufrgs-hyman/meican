<?php

namespace meican\modules\aaa\assets;

use yii\web\AssetBundle;

class AccountAsset extends AssetBundle
{
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	
    public $js = [
    	'js/aaa/user.js',
    ];
    
    public $depends = [
    	'meican\assets\MeicanAsset',
	];
}
