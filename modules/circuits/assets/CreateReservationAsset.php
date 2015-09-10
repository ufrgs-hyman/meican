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
        'js/vendor/jquery.hoverIntent.minified.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
    	'app\assets\MeicanAsset',
        'app\assets\MeicanMapAsset',
        'app\assets\TimePickerAsset',
    ];
}
