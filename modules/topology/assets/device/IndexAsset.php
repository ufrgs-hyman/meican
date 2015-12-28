<?php

namespace meican\topology\assets\device;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/public';

    public $js = [
    	'device/device.js',
    ];
    public $depends = [
    	'yii\web\JqueryAsset',
    ];
}
