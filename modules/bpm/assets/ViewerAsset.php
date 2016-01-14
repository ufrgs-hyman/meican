<?php

namespace meican\bpm\assets;

use yii\web\AssetBundle;

class ViewerAsset extends AssetBundle
{
    public $sourcePath = '@meican/bpm/assets/public';
    
    public $css = [
    ];
    
    public $js = [
    	'viewer.js',
    ];
    
    public $depends = [
		'yii\web\JqueryAsset',
    ];
 
}

