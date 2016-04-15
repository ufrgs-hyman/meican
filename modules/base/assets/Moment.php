<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Moment extends AssetBundle
{
    public $sourcePath = '@bower/moment/min';
    
    public $js = [
        'moment.min.js'
    ];
    
    public $css = [
    ];
    
    public $depends = [
    ];
}
