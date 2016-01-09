<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'meican\base\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
