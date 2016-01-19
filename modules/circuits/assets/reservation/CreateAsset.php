<?php

namespace meican\circuits\assets\reservation;

use yii\web\AssetBundle;

class CreateAsset extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/reservation/public';
    
    public $js = [
    	'create/create2.js',
    ];
    
    public $depends = [
        'meican\base\assets\layout\Asset',
        'meican\topology\assets\map\MeicanLMapAsset',
    ];
}
