<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\notification;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'meican\notification\controllers';

    public $defaultRoute = 'notification';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations() {
        Yii::$app->i18n->translations['notification*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@meican/notification/messages',
            'fileMap' => [
                'notification' => 'notification.php',
            ],
        ];
    }
}
