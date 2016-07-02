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
class GMap extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/viewer/map';

    public $js = [
        'meican-gmap.js',
    ];
    public $depends = [
        'meican\base\assets\google\Maps',
    ];
}
