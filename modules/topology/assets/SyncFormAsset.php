<?php

namespace app\modules\topology\assets;

use yii\web\AssetBundle;

class SyncFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/topology/sync/sync-form.js',
        'js/vendor/jquery-cron.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

?>