<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\aaa\assets\login;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/login/public';
    
    public $js = [
        'login.js',
    ];
    
    public $depends = [
        'meican\base\assets\layout\Asset',
    ];
}
