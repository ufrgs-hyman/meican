<?php

namespace meican\topology\assets;

use yii\web\AssetBundle;

class ChangeAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/sync/public';

    public $js = [
        'changes.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

?>