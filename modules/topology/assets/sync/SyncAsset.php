<?php

namespace meican\topology\assets\sync;

use yii\web\AssetBundle;

class SyncAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/public';

    public $js = [
        'sync/sync.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

?>