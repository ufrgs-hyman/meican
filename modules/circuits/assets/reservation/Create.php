<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\assets\reservation;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Create extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/reservation/public/create';
    
    public $js = [
        'create-i18n.js',
    	'create.js',
    ];

    public $css = [
        'create.css',
        #'/meican/topology/viewer/map-domain-colors'
    ];

    public $depends = [
        'meican\base\assets\Theme',
        'meican\base\assets\FullCalendar',
        'meican\base\assets\DateRangePicker',
        'meican\topology\assets\viewer\LMap',
        'meican\topology\assets\viewer\VGraph',
        'meican\base\assets\LSidebar'
    ];
}
