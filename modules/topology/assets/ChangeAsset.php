<?php

namespace app\modules\topology\assets;

use yii\web\AssetBundle;

class ChangeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/topology/sync/changes.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

?>