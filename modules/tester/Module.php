<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\tester;

use Yii;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'meican\tester\controllers';

    public $defaultRoute = 'tester';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations() {
        Yii::$app->i18n->translations['notification*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@meican/tester/messages',
            'fileMap' => [
                'messages' => 'messages.php',
            ],
        ];
    }
}
