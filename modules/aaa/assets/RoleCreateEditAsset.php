<?php

namespace app\modules\aaa\assets;

use yii\web\AssetBundle;

class RoleCreateEditAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/aaa/roleCreateEdit.js',
    ];
    
    public $depends = [
    		'yii\web\JqueryAsset',
    ];
}
