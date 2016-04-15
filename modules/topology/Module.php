<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology;

use Yii;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Module extends \yii\base\Module {

    public $controllerNamespace = 'meican\topology\controllers';

    public function init() {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations() {
        Yii::$app->i18n->translations['topology*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@meican/topology/messages',
            'fileMap' => [
                'topology' => 'topology.php',
            ],
        ];
    }
}
