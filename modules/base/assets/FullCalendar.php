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
class FullCalendar extends AssetBundle
{
    public $sourcePath = '@bower/fullcalendar/dist';
    
    public $js = [
        'fullcalendar.min.js',
    	'lang-all.js'
    ];
    
    public $css = [
    	'fullcalendar.min.css',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    	'meican\base\assets\Moment',
    ];
}
