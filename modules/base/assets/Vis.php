<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Vis extends AssetBundle
{
    public $sourcePath = '@npm/vis/dist';
    
    public $js = [
        'vis.min.js',
    ];
    
    public $css = [
        'vis.min.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
