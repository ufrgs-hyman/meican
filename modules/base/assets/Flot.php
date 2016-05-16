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
    public $sourcePath = '@npm/flot';
    
    public $js = [
        'jquery.flot.min.js',
        'jquery.flot.resize.min.js',
        'jquery.flot.time.min.js'
    ];
    
    public $css = [
    ];
    
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
