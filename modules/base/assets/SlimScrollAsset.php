<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class SlimScrollAsset extends AssetBundle
{
    public $sourcePath = '@bower/slimscroll';
    
    public $js = [
        'jquery.slimscroll.min.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
