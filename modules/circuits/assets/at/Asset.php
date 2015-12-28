<?php

namespace meican\circuits\assets\at;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/at/public';
    
    public $js = [
    	'at-i18n.js',
    	'at.js',
    ];
    
    public $depends = [
        'meican\base\assets\CronPickerAsset',
    ];
}
