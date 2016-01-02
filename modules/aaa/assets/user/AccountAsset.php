<?php

namespace meican\aaa\assets\user;

use yii\web\AssetBundle;

class AccountAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/user/public';
	
    public $js = [
    	'account.js',
    ];
    
    public $depends = [
    	'meican\base\assets\layout\Asset',
    ];
}
