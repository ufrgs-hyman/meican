<?php

namespace app\modules\topology\assets;

use yii\web\AssetBundle;

class SyncAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/topology/sync/sync.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

?>