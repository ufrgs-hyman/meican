<?php

namespace app\modules\circuits\assets;

use yii\web\AssetBundle;

class ListReservationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
   		'css/pagination.css',
    ];
    
    public $js = [
    		'js/circuits/list.js',
    ];
    
    public $depends = [
   		'yii\web\JqueryAsset',
    ];
}
