<?php

namespace meican\modules\topology\assets;

use yii\web\AssetBundle;

class ViewerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/topology/viewer-i18n.js',
    	'js/topology/viewer.js',
    ];

    public $depends = [
        'meican\assets\MeicanAsset',
        'meican\assets\MeicanMapAsset',
    ];
}
