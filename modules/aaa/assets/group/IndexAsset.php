<?php

namespace meican\aaa\assets\group;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/group/public';

    public $js = [
        'group.js',
    ];

    public $depends = [
        'meican\base\assets\layout\Asset',
    ];
}
