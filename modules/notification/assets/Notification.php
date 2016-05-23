<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\notification\assets;

use yii\web\AssetBundle;

/**
 * @author Diego Pittol
 * @author Maurício Quatrin Guerreiro
 */
class Notification extends AssetBundle
{
    public $sourcePath = '@meican/notification/assets/public';

    public $css = [
		'notification.css',
    ];
    
    public $js = [
		'notification.js',
    ];
    
    public $depends = [
        'kartik\base\AnimateAsset',
        'kartik\growl\GrowlAsset',
        'yii\web\JqueryAsset',
    ];
}
