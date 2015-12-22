<?php

namespace meican\modules\circuits\assets;

use yii\web\AssetBundle;

class AutomatedTestAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
    	'js/circuits/tests/tests-i18n.js',
    	'js/circuits/tests/tests.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
		'meican\assets\MeicanAsset',
        'meican\assets\CronPickerAsset',
    ];
}
