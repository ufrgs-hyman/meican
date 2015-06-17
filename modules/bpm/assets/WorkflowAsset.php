<?php

namespace app\modules\bpm\assets;

use yii\web\AssetBundle;

class WorkflowAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
    		
    ];
    public $depends = [
		'yii\web\JqueryAsset',
    ];

}

