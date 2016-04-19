<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\assets;

use yii\web\AssetBundle;

/**
 * @author Maurício Quatrin Guerreiro
 */
class LSidebar extends AssetBundle
{
    public $sourcePath = '@meican/base/assets/lsidebar/public';
    
    public $css = [
        'sidebar.css',
    ];
    
    public $js = [
        'sidebar.js',
    ];
    
    public $depends = [
        'meican\base\assets\Leaflet',
    ];
}

?>