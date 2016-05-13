<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class MetricsGraphics extends AssetBundle
{
    public $sourcePath = '@bower/metrics-graphics/dist';
    
    public $js = [
        'metricsgraphics.min.js'
    ];
    
    public $css = [
        'metricsgraphics.css'
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
        'meican\base\assets\D3'
    ];
}
