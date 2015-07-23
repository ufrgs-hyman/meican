<?php

namespace app\modules\circuits\assets;

use yii\web\AssetBundle;

class ViewReservationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
   		'css/pagination.css',
    ];
    
    public $js = [
    	'js/circuits/reservation/view-i18n.js',
    	'js/circuits/reservation/view.js',
    	'js/google/styled.marker.js',
    	'js/google/marker.clusterer.compiled.js',
        'js/maps/meican-maps.js',
    ];
}

?>