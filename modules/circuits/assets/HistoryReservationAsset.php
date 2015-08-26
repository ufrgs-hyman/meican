<?php

namespace app\modules\circuits\assets;

use yii\web\AssetBundle;

class HistoryReservationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
   		'css/pagination.css',
    ];
    
    public $depends = [
   		'yii\web\JqueryAsset',
    ];
}
