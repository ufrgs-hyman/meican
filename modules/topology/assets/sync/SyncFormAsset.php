<?php

namespace meican\modules\topology\assets;

use yii\web\AssetBundle;

class SyncFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/topology/sync/sync-form.js',
        'js/topology/sync/sync-form-i18n.js',
    ];

    public $depends = [
        'meican\assets\MeicanAsset',
        'meican\assets\CronPickerAsset',
    ];
}

?>