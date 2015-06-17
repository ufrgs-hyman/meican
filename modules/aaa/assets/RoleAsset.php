<?php

namespace app\modules\aaa\assets;

use yii\web\AssetBundle;

class RoleAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/aaa/role.js',
    ];
    
    public $css = [ 
		'css/pagination.css',
    ];
}
