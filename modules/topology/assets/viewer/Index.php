<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\assets\viewer;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Index extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/viewer/index';

    public $js = [
    	'viewer2.js',
    ];

    public $depends = [
    	'meican\base\assets\Theme',
        'meican\topology\assets\viewer\LMap',
        'meican\topology\assets\viewer\VGraph',
        'meican\base\assets\LSidebar',
        'yii\web\YiiAsset'
    ];
}
