<?php

namespace meican\aaa\assets\account;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/account/public';
	
    public $js = [
    	'account.js',
    ];
    
    public $depends = [
    	'meican\base\assets\layout\Asset',
    ];
}
