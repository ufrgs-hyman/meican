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
class Feedback extends AssetBundle {
    
    public $sourcePath = '@meican/base/assets/feedback';

    public $js = [
        'feedback.js'
    ];
    
    public $css = [
    ];
    
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\widgets\ActiveFormAsset',
        'yii\validators\ValidationAsset',
    ];
}
