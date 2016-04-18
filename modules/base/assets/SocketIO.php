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
class SocketIO extends AssetBundle {
    
    public $js = [
        'https://cdn.socket.io/socket.io-1.4.5.js',
    ];
    
    public $css = [
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
