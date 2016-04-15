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
class Ionicons extends AssetBundle
{
    public $sourcePath = '@bower/ionicons';
    
    public $js = [
    ];
    
    public $css = [
        'css/ionicons.min.css',
    ];
    
    public $depends = [
    ];
}
