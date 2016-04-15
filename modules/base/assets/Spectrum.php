<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Spectrum extends AssetBundle
{
    public $sourcePath = '@bower/spectrum';
    
    public $js = [
        'spectrum.js',
    ];
    
    public $css = [
        'spectrum.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
