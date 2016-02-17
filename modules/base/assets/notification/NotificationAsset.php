<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\assets\notification;

use yii\web\AssetBundle;

/**
 * Base theme of the application.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class NotificationAsset extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/notification/public';

    public $css = [
		'notification.css',
    ];
    
    public $js = [
		'notification.js',
    ];
    
    public $depends = [
        'meican\base\assets\Theme',
    ];
}
