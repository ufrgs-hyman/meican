<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\assets\connection;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class View extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/connection/public';
    
    public $js = [
        'view2.js'
    ];

    public $css = [
    ];

    public $depends = [
        'meican\base\assets\Theme',
        'meican\topology\assets\map\MeicanLMap',
        'meican\topology\assets\graph\MeicanVGraph',
        'meican\base\assets\Moment',
    ];
}

?>