<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\assets\map;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class MeicanGMapAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/map/public';

    public $js = [
        'meican-google-map.js',
    ];
    public $depends = [
        'meican\base\assets\google\MapsAsset',
    ];
}
