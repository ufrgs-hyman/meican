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
class MeicanGraph extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/graph/public';

    public $js = [
        'meican-graph.js',
    ];
    public $depends = [
        'meican\base\assets\Vis',
    ];
}

?>