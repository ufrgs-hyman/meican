<?php

namespace app\modules\circuits\assets;

use yii\web\AssetBundle;

class StatusReservationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
   		'css/pagination.css',
    ];
    
    public $js = [
    		'js/circuits/reservation/status-i18n.js',
    		'js/circuits/reservation/status.js',
    ];
    
    public $depends = [
   		'yii\web\JqueryAsset',
    ];
}
