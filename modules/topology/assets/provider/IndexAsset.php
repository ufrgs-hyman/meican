<?php

namespace meican\topology\assets\provider;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/public';

    public $js = [
    	'provider/provider.js',
    ];
}
