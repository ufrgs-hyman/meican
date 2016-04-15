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
class Icheck extends AssetBundle
{
    public $sourcePath = '@bower/icheck';
    
    public $js = [
        'icheck.min.js'
    ];
    
    public $css = [
        'skins/minimal/blue.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
