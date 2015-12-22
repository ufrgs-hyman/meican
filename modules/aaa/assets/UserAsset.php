<?php

namespace meican\modules\aaa\assets;

use yii\web\AssetBundle;

class UserAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/aaa/user.js',
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset',
    ];
}
