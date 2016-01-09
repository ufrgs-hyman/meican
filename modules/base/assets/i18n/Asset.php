<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\assets\i18n;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/i18n/public';
    
    public $js = [
        'meican-i18n.js',
    ];
    
}
