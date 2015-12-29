<?php

namespace meican\topology\assets\network;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/network/public';

    public $js = [
    	'network.js',
    ];
    public $depends = [
		'meican\base\assets\layout\Asset',
    ];
}
