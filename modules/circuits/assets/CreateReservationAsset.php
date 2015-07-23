<?php

namespace app\modules\circuits\assets;

use yii\web\AssetBundle;

class CreateReservationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
    	'js/circuits/reservation/create-i18n.js',
    	'js/circuits/reservation/create.js',
    	'js/circuits/reservation/recurrence.js',
    	'js/google/styled.marker.js',
    	'js/google/marker.clusterer.compiled.js',
    	'js/jquery.timepicker.min.js',
        'js/maps/meican-maps.js',
    ];
    
    public $css = [
   		'css/jquery.timepicker.css',
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset',
    ];
}
