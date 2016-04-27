<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\assets\user;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Update extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/user/public';
	
    public $js = [
    	'account.js',
    ];
    
    public $depends = [
    	'meican\base\assets\Theme',
    ];
}
