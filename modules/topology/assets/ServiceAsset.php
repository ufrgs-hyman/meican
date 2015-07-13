<?php

namespace app\modules\topology\assets;

use yii\web\AssetBundle;

class ServiceAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/topology/service.js',
    ];
}
