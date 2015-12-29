<?php

namespace meican\topology\assets\provider;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/provider/public';

    public $js = [
    	'provider.js',
    ];

    public $depends = [
		'meican\base\assets\layout\Asset',
    ];
}
