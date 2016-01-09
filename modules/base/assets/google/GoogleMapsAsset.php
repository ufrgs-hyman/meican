<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\assets\google;

use yii\web\AssetBundle;
use Yii;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class GoogleMapsAsset extends AssetBundle
{
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    static function getMapsUrl() {
        return 'https://maps.googleapis.com/maps/api/js?v=3&libraries=places&language='.Yii::$app->language;
    }

    static function getMapsKey() {
        if (Yii::$app->params['google.maps.key']) {
            return '&key='.\Yii::$app->params['google.maps.key'];
        }
        return '';
    }

    public function registerAssetFiles($view)
    {
        $manager = $view->getAssetManager();
        $view->registerJsFile($manager->getAssetUrl($this, $this->getMapsUrl()), $this->jsOptions);
        foreach ($this->css as $css) {
            $view->registerCssFile($manager->getAssetUrl($this, $css), $this->cssOptions);
        }
    }
}

?>