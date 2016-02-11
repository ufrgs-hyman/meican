<?php

namespace meican\circuits\assets\reservation;

use yii\web\AssetBundle;

class CreateAsset extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/reservation/public/create';
    
    public $js = [
    	'create2.js',
        'sidebar.js',
    ];

    public $css = [
        'create.css',
        'sidebar.css',
    ];

    public $depends = [
        'meican\base\assets\layout\Asset',
        'meican\base\assets\ToggleAsset',
        'meican\topology\assets\map\MeicanLMap',
        'meican\topology\assets\graph\MeicanGraph'
    ];
}
