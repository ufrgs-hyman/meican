<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\assets\reservation;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Create extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/reservation/public/create';
    
    public $js = [
        'create-i18n.js',
    	'create2.js',
    ];

    public $css = [
        'create.css',
    ];

    public $depends = [
        'meican\base\assets\Theme',
        'meican\base\assets\FullCalendar',
        'meican\base\assets\DateRangePicker',
        'meican\topology\assets\map\LMap',
        'meican\topology\assets\graph\VGraph',
        'meican\base\assets\LSidebar'
    ];
}
