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
class FontAwesome extends AssetBundle
{
    public $sourcePath = '@bower/fontawesome';
    
    public $js = [
    ];
    
    public $css = [
        'css/font-awesome.min.css'
    ];
    
    public $depends = [
    ];
}
