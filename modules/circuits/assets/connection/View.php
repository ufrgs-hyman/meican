<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
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
        'meican\topology\assets\map\LMap',
        'meican\topology\assets\graph\VGraph',
        'meican\base\assets\Moment',
        'meican\base\assets\MetricsGraphics',
        'meican\base\assets\Flot'
    ];
}

?>