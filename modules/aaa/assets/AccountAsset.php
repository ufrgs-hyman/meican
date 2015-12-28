<?php

namespace meican\aaa\assets;

use yii\web\AssetBundle;

class AccountAsset extends AssetBundle
{
	public $sourcePath = '@meican/aaa/assets/public';
	
    public $js = [
    	'account/account.js',
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset',
	];
}
