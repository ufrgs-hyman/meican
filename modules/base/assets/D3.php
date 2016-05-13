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
class D3 extends AssetBundle
{
    public $sourcePath = '@bower/d3';
    
    public $js = [
        'd3.min.js'
    ];
    
    public $css = [
    ];
    
    public $depends = [
    ];
}
