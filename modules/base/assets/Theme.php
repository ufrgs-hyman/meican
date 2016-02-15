<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * Base theme of the application.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Theme extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/theme/public';

    public $css = [
        'layout.css',
        'theme.css',
    ];
    
    public $js = [
        'layout.js',
    ];
    
    public $depends = [
        'meican\base\assets\theme\IcheckTheme',
        'yii\bootstrap\BootstrapPluginAsset',
        'meican\base\assets\SlimScroll',
        'meican\base\assets\FontAwesome',
        'meican\base\assets\Ionicons',
    ];
}
