<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\bpm\assets;

use yii\web\AssetBundle;

/**
 * @author Diego Pittol
 */
class ViewerEditor extends AssetBundle
{
    public $sourcePath = '@meican/bpm/assets/public';
    
    public $css = [
        'workflowViewer.css'
    ];

    public $js = [
        'moment.js',
        'moment-timezone.js',
        'MeicanContainer.js',
        'workflowLanguageViewer.js'
    ];

    public $depends = [
        'meican\bpm\assets\WireIt',
    ];
}

