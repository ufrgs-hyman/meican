<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\assets\discovery;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Rule extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/discovery/public';

    public $js = [
        'rule.js',
    ];

    public $depends = [
        'meican\base\assets\Theme',
        'meican\base\assets\CronPicker'
    ];
}
