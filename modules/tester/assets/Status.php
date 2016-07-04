<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\tester\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Status extends AssetBundle
{
    public $sourcePath = '@meican/tester/assets/public';
    
    public $js = [
    	'status-i18n.js',
    	'status.js',
    ];
    
    public $depends = [
        'meican\base\assets\Theme',
        'meican\base\assets\CronPicker',
    ];
}
