<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\assets\map;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class LMap extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/map/public';

    public $js = [
        'meican-lmap.js',
    ];
    public $depends = [
        'meican\base\assets\LeafletCluster',
    ];
}
