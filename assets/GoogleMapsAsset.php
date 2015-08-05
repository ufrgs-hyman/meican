<?php

namespace app\assets;

use yii\web\AssetBundle;

class GoogleMapsAsset extends AssetBundle
{
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
    
    public $js = [
        'https://maps.googleapis.com/maps/api/js?v=3&libraries=places',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

?>