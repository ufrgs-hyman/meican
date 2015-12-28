<?php

namespace meican\circuits\assets\authorization;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/authorization/public';
    
    public $js = [
    	'authorization.js',
    	'authorization-i18n.js',
    ];
    
    public $css = [
		'authorization.css',
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset',
    ];
}

?>