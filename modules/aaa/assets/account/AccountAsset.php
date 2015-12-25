<?php

namespace meican\modules\aaa\assets\account;

use yii\web\AssetBundle;

class AccountAsset extends AssetBundle
{
	public $sourcePath = '@meican/modules/aaa/assets/account/public';
	
    public $js = [
    	'account.js',
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset',
	];
}
