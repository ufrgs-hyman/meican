<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\assets\role;

use yii\web\AssetBundle;

/**
 * @author Diego Pittol
 */
class SystemRole extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/role/public';

    public $js = [
    	'roleSystem.js',
    ];
    
    public $depends = [
        'meican\base\assets\Theme',
    ];
}
