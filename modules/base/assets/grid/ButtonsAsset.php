<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\assets\grid;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ButtonsAsset extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/grid/public';

    public $js = [
        'grid-buttons.js',
    ];
    
    public $depends = [
        'meican\base\assets\Theme',
    ];
}
