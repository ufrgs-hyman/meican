<?php

namespace meican\modules\circuits\assets;

use yii\web\AssetBundle;

class AuthorizationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
    	'js/circuits/authorization.js',
    	'js/circuits/authorization-i18n.js',
    ];
    
    public $css = [
    		'css/authorization.css',
    		'css/pagination.css',
    ];
    
    public $depends = [
    	//'yii\web\YiiAsset',
    	//'yii\bootstrap\BootstrapAsset',
    	'yii\web\JqueryAsset',
    ];
}

?>