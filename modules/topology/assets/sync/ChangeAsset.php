<?php

namespace meican\topology\assets\sync;

use yii\web\AssetBundle;

class ChangeAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/sync/public';

    public $js = [
        'changes.js',
    ];

    public $depends = [
		'meican\base\assets\Theme',
    ];
}

?>