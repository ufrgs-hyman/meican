<?php

namespace meican\bpm\assets;

use yii\web\AssetBundle;

class WorkflowAsset extends AssetBundle
{
    public $sourcePath = '@meican/bpm/assets/public';
    
    public $css = [
    ];
    
    public $js = [
    ];
    
    public $depends = [
		'yii\web\JqueryAsset',
    ];
}

