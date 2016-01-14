<?php

namespace meican\bpm\assets;

use yii\web\AssetBundle;

class CreateAsset extends AssetBundle
{
    public $sourcePath = '@meican/bpm/assets/public';
    
    public $css = [
    ];
    public $js = [
    	'create.js',
    ];
    public $depends = [
		'yii\web\JqueryAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}

