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
class IcheckTheme extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/layout/public';

    public $js = [
        'icheck-theme.js',
    ];
    
    public $depends = [
        'meican\base\assets\Icheck'
    ];
}
