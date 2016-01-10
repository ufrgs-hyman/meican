<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\assets\domain;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class FormAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/domain/public';

    public $js = [
        'form.js',
    ];

    public $css = [
        'form.css',
    ];

    public $depends = [
        'meican\base\assets\layout\Asset',
        'meican\base\assets\SpectrumAsset',
    ];
}
