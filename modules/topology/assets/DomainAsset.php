<?php

namespace meican\modules\topology\assets;

use yii\web\AssetBundle;

class DomainAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
    	'js/topology/domain.js',
    ];
}
