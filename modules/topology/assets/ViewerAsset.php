<?php

namespace app\modules\topology\assets;

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
        'app\assets\MeicanAsset',
        'app\assets\MeicanMapAsset',
    ];
}
