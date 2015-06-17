<?php

namespace app\modules\topology\assets;

use yii\web\AssetBundle;

class ImportAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/topology/import.js',
    ];
    
    public $depends = [
    'yii\web\JqueryAsset',
    ];
}
