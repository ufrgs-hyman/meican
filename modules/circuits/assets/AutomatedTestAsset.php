<?php

namespace app\modules\circuits\assets;

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
		'app\assets\MeicanAsset',
        'app\assets\CronPickerAsset',
    ];
}
