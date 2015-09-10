<?php

namespace app\modules\topology\assets;

use yii\web\AssetBundle;

class SyncFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/topology/sync/sync-form.js',
    ];

    public $depends = [
        'app\assets\MeicanAsset',
        'app\assets\CronPickerAsset',
    ];
}

?>