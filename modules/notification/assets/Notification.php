<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\notification\assets;

use yii\web\AssetBundle;

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
        'yii\web\JqueryAsset',
    ];
}
