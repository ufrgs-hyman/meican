<?php

namespace app\modules\aaa\assets;

use yii\web\AssetBundle;

class GroupCreateEditAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/aaa/groupCreateEdit.js',
    ];
    
    public $depends = [
    		'yii\web\JqueryAsset',
    ];
}
