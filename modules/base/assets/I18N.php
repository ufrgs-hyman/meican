<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * Javascript module for message translation (i18n) support.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class I18N extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/i18n/public';
    
    public $js = [
        'meican-i18n.js',
    ];
}
