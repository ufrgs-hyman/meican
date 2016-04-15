<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class TimePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/timepicker';
    
    public $js = [
        'jquery.timepicker.min.js',
    ];
    
    public $css = [
        'jquery.timepicker.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
