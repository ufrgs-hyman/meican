<?php

namespace meican\modules\aaa\assets;

use yii\web\AssetBundle;

class UserAsset extends AssetBundle
{
    public $sourcePath = '@meican/modules/aaa/assets';

    public $js = [
    	'js/user.js',
    ];
    
    public $depends = [
    ];
}
