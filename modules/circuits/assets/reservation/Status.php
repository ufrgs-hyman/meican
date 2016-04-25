<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\assets\reservation;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Status extends AssetBundle
{
    public $sourcePath = '@meican/circuits/assets/reservation/public';
    
    public $css = [
    ];
    
    public $js = [
		'status/status-i18n.js',
		'status/status.js',
    ];
    
    public $depends = [
   		'yii\web\JqueryAsset',
    ];
}
