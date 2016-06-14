<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Flot extends AssetBundle
{
    public $sourcePath = '@npm/flot-charts';
    
    public $js = [
        'jquery.flot.js',
        'jquery.flot.resize.js',
        'jquery.flot.time.js',
        'jquery.flot.stack.js',
        'jquery.flot.crosshair.js'
    ];
    
    public $css = [
    ];
    
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
