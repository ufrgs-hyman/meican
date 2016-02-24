<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\notification\assets;

use yii\web\AssetBundle;

/**
 * Base theme of the application.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class NotificationAsset extends AssetBundle
{
    public $sourcePath = '@meican/notification/assets/public';

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
