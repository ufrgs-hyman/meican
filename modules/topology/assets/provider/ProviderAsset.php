<?php

namespace meican\modules\topology\assets;

use yii\web\AssetBundle;

class ProviderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/topology/provider.js',
    ];
}
