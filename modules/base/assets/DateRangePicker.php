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
class DateRangePicker extends AssetBundle
{
    public $sourcePath = '@bower/bootstrap-daterangepicker';
    
    public $js = [
        'daterangepicker.js'
    ];
    
    public $css = [
        'daterangepicker.css'
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
        'meican\base\assets\Moment'
    ];
}
