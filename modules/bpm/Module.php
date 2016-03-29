<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\bpm;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'meican\bpm\controllers';

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations() {
        Yii::$app->i18n->translations['bpm*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@meican/bpm/messages',
            'fileMap' => [
                'bpm' => 'bpm.php',
            ],
        ];
    }
}
