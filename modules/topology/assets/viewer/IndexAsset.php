<?php

namespace meican\topology\assets\viewer;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/public';

    public $js = [
    	'viewer/viewer-i18n.js',
    	'viewer/viewer.js',
    ];

    public $depends = [
        'meican\base\assets\map\MeicanMapAsset',
    ];
}
