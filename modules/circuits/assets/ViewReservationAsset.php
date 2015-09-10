<?php

namespace app\modules\circuits\assets;

use yii\web\AssetBundle;

class ViewReservationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
    ];
    
    public $js = [
    	'js/circuits/reservation/view-i18n.js',
    	'js/circuits/reservation/view.js',
    	
    ];

    public $depends = [
        'app\assets\MeicanAsset',
        'app\assets\MeicanMapAsset'
    ];
}

?>