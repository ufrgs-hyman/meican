<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\monitoring;

use Yii;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'meican\monitoring\controllers';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations() {
        Yii::$app->i18n->translations['notification*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@meican/monitoring/messages',
            'fileMap' => [
                'messages' => 'messages.php',
            ],
        ];
    }
}
