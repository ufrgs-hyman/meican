<?php

namespace meican\circuits\assets\reservation;

use yii\web\AssetBundle;

class CreateAsset extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/reservation/public';
    
    public $js = [
    	'create/create-i18n.js',
    	'create/create.js',
    	'create/recurrence.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
    	'meican\base\assets\MeicanAsset',
        'meican\base\assets\MeicanMapAsset',
        'meican\base\assets\TimePickerAsset',
        'meican\base\assets\HoverIntentAsset'
    ];
}
