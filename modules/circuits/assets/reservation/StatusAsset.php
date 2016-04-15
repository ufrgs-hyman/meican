<?php

namespace meican\circuits\assets\reservation;

use yii\web\AssetBundle;

class StatusAsset extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/reservation/public';
    
    public $css = [
    ];
    
    public $js = [
		'status/status-i18n.js',
		'status/status.js',
    ];
    
    public $depends = [
   		'yii\web\JqueryAsset',
    ];
}
