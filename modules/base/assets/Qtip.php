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
class Qtip extends AssetBundle
{
    public $sourcePath = '@bower/qtip2-main/dist';
    
    public $js = [
        'jquery.qtip.min.js',
    ];
    
    public $css = [
        'jquery.qtip.min.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
