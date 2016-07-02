<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\assets\viewer;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class VGraph extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/viewer/graph';

    public $js = [
        'meican-vgraph.js',
    ];

    public $css = [
        'graph.css'
    ];

    public $depends = [
        'meican\base\assets\Vis',
        'meican\base\assets\Qtip',
    ];
}

?>