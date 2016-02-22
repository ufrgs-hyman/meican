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
        'sidebar.js',
    ];

    public $css = [
        'create.css',
        'sidebar.css',
    ];

    public $depends = [
        'meican\base\assets\Theme',
        'meican\base\assets\ToggleAsset',
        'meican\base\assets\DateRangePicker',
        'meican\topology\assets\map\MeicanLMap',
        'meican\topology\assets\graph\MeicanVGraph'
    ];
}
