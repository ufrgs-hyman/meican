<?php

namespace app\modules\aaa\assets;

use yii\web\AssetBundle;

class GroupAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/aaa/group.js',
    ];
}
