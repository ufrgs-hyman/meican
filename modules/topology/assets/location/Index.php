<?php
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\assets\location;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Index extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/location/public';

    public $js = [
    	'location.js',
    ];
    public $depends = [
    	'meican\base\assets\Theme',
    ];
}
