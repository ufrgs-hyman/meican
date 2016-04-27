<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\assets\authorization;

use yii\web\AssetBundle;

class Detailed extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/authorization/public';
    
    public $js = [
    	'authorization.js',
    ];
    
    public $css = [
		'authorization.css',
    ];
    
    public $depends = [
    	'yii\web\JqueryAsset',
        'meican\base\assets\FullCalendar'
    ];
}

?>