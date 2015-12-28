<?php

namespace meican\topology\assets\sync;

use yii\web\AssetBundle;

class SyncFormAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/public';

    public $js = [
        'sync/sync-form.js',
        'sync/sync-form-i18n.js',
    ];

    public $depends = [
        'meican\base\assets\CronPickerAsset',
    ];
}

?>