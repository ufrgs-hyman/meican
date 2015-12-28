<?php

namespace meican\circuits\assets\reservation;

use yii\web\AssetBundle;

class ViewAsset extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/reservation/public';
    
    public $css = [
    ];
    
    public $js = [
    	'view/view-i18n.js',
    	'view/view.js',
    	
    ];

    public $depends = [
        'meican\base\assets\MeicanMapAsset'
    ];
}

?>