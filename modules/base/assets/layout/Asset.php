<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\assets\layout;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/layout/public';

    public $css = [
        'layout.css',
        'theme.css',
    ];
    
    public $js = [
        'layout.js',
    ];
    
    public $depends = [
        'meican\base\assets\layout\IcheckTheme',
        'yii\bootstrap\BootstrapPluginAsset',
        'meican\base\assets\SlimScroll',
        'meican\base\assets\FontAwesome',
        'meican\base\assets\Ionicons',
    ];
}
