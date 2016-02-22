<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\assets\graph;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class MeicanVGraph extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/graph/public';

    public $js = [
        'meican-vgraph.js',
    ];
    public $depends = [
        'meican\base\assets\Vis',
        'meican\base\assets\Qtip',
    ];
}

?>