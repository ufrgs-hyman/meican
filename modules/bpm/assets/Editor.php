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
class Editor extends AssetBundle
{
    public $sourcePath = '@meican/bpm/assets/public';
    
    public $css = [
        'workflow.css'
    ];

    public $js = [
        'moment.js',
        'moment-timezone.js',
        'MeicanContainer.js',
        'workflowLanguage.js'
    ];

    public $depends = [
        'meican\bpm\assets\WireIt',
    ];
}

