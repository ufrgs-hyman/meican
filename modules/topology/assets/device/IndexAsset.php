<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\assets\device;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/device/public';

    public $js = [
    	'device.js',
    ];
    public $depends = [
    	'meican\base\assets\Theme',
    ];
}
