<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits;

use Yii;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'meican\circuits\controllers';

    public $defaultRoute = 'connection';

    public function init() {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations() {
        Yii::$app->i18n->translations['circuits*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@meican/circuits/messages',
            'fileMap' => [
                'circuits' => 'circuits.php',
            ],
        ];
    }
    
}
