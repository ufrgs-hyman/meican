<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\home;

use Yii;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'meican\home\controllers';

    public $defaultRoute = 'board';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations() {
        Yii::$app->i18n->translations['home*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@meican/home/messages',
            'fileMap' => [
                'home' => 'home.php',
            ],
        ];
    }
}
