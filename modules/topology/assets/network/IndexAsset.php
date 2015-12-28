<?php

namespace meican\topology\assets\network;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/public';

    public $js = [
    	'network/network.js',
    ];
    public $depends = [
		'yii\web\JqueryAsset',
    ];
}
