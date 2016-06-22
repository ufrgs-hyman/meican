<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\notify\assets;

use yii\web\AssetBundle;

/**
 * @author Diego Pittol
 * @author Maurício Quatrin Guerreiro
 */
class Notify extends AssetBundle
{
    public $sourcePath = '@meican/notify/assets/public';

    public $css = [
		'notify.css',
    ];
    
    public $js = [
		'notify.js',
        'alert.js'
    ];
    
    public $depends = [
        'kartik\base\AnimateAsset',
        'kartik\growl\GrowlAsset',
        'yii\web\JqueryAsset',
    ];
}
