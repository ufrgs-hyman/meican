<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets\leaflet;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Label extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/leaflet/label/dist';
    
    public $js = [
        'leaflet.label.js'
    ];
    
    public $css = [
        'leaflet.label.css'
    ];
    
    public $depends = [
        'meican\base\assets\leaflet\Map'
    ];
}

?>