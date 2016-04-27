<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\assets\config;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Index extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/config/public';
    
    public $js = [
        'config.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
