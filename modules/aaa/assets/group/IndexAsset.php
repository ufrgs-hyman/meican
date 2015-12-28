<?php

namespace meican\aaa\assets\group;

use yii\web\AssetBundle;

class IndexAsset extends AssetBundle
{
    public $sourcePath = '@meican/aaa/assets/public';

    public $js = [
    	'group/group.js',
    ];

    public $depends = [
    	'yii\web\JqueryAsset',
    ];
}
