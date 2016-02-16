<?php

namespace meican\topology\assets\sync;

use yii\web\AssetBundle;

class SyncFormAsset extends AssetBundle
{
    public $sourcePath = '@meican/topology/assets/sync/public';

    public $js = [
        'sync-form.js',
        'sync-form-i18n.js',
    ];

    public $depends = [
    	'meican\base\assets\Theme',
        'meican\base\assets\cron\PickerAsset',
    ];
}

?>