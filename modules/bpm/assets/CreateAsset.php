<?php

namespace app\modules\bpm\assets;

use yii\web\AssetBundle;

class CreateAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
    	'js/bpm/workflow/create.js',
    ];
    public $depends = [
		'yii\web\JqueryAsset',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}

