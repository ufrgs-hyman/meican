<?php

namespace meican\circuits\assets\reservation;

use yii\web\AssetBundle;

class Create extends AssetBundle
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
        'meican\base\assets\Theme',
        'meican\base\assets\Qtip',
        'meican\base\assets\ToggleAsset',
        'meican\topology\assets\map\MeicanLMap',
        'meican\topology\assets\graph\MeicanVGraph'
    ];
}
