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
class WorkflowAsset extends AssetBundle
{
    public $sourcePath = '@meican/bpm/assets/public';
    
    public $css = [
    ];
    
    public $js = [
    ];
    
    public $depends = [
		'yii\web\JqueryAsset',
    ];
}

