<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\notify;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'meican\notify\controllers';

    public $defaultRoute = 'notify';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations() {
        Yii::$app->i18n->translations['notify*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@meican/notify/messages',
            'fileMap' => [
                'notify' => 'notify.php',
            ],
        ];
    }
}
