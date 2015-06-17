<?php

namespace app\modules\topology\assets;

use yii\web\AssetBundle;

class NetworkAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/topology/network.js',
    ];
    public $depends = [
		'yii\web\JqueryAsset',
    ];
}
