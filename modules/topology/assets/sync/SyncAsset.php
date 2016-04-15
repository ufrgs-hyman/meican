<?php

namespace meican\topology\assets\sync;

use yii\web\AssetBundle;

class SyncAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/sync/public';

    public $js = [
        'sync.js'
    ];

    public $depends = [
		'meican\base\assets\Theme',
    ];
}

?>