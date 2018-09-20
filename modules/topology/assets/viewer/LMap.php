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
class LMap extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/viewer/map';

    public $js = [
        'meican-lmap.js',
    ];

    public $css = [
        'lmap-label.css'
    ];

    public $depends = [
        'meican\base\assets\leaflet\Cluster',
        'meican\base\assets\leaflet\TextPath'
    ];
}
