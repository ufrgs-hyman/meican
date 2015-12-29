<?php

namespace meican\topology\assets\device;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/device/public';

    public $js = [
    	'device.js',
    ];
    public $depends = [
    	'meican\base\assets\layout\Asset',
    ];
}
