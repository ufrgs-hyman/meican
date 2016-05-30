<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * MEICAN base asset.
 *
 * Includes I18N system, bootstrap scripts and styles, Icheck and
 * Icheck theme, slimscroll, Ionicons, Fontawesome and Notify assets.
 * Also includes all scripts and base styles for the base layout of 
 * the application. Based on AdminLTE amazing template.
 *
 * Each module may be require more assets. Do not put all module
 * exclusive assets in this asset bundle class. For each page must
 * exist one exclusive asset class reporting all scripts and styles 
 * required by that page. So, for example, in the case of a about
 * page, must exist a About asset and this asset must include in
 * your assets requirements (depends array) this base asset (Theme).
 *
 * @author Maurício Quatrin Guerreiro
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
        'meican\base\assets\I18N',
        'meican\notify\assets\Notify',
        'meican\base\assets\Feedback'
    ];
}
