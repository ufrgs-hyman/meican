<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\assets\service;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Index extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/service/public';

    public $js = [
        'service.js',
    ];

    public $depends = [
		'meican\base\assets\Theme',
    ];
}
