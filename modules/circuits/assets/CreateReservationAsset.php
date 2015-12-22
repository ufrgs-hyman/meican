<?php

namespace meican\modules\circuits\assets;

use yii\web\AssetBundle;

class CreateReservationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
    	'js/circuits/reservation/create-i18n.js',
    	'js/circuits/reservation/create.js',
    	'js/circuits/reservation/recurrence.js',
        'js/vendor/jquery.hoverIntent.minified.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
    	'meican\assets\MeicanAsset',
        'meican\assets\MeicanMapAsset',
        'meican\assets\TimePickerAsset',
    ];
}
