<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets\leaflet;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Cluster extends AssetBundle
{
    public $sourcePath = '@npm/leaflet.markercluster/dist';
    
    public $css = [
        'MarkerCluster.css',
        'MarkerCluster.Default.css'
    ];
    
    public $js = [
        'leaflet.markercluster.js',
    ];
    
    public $depends = [
        'meican\base\assets\leaflet\Map',
    ];
}

?>