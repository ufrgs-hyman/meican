<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\models;

use Yii;

use meican\topology\models\Service;

/**
 * @author Maurício Quatrin Guerreiro
 */
class Protocol {

    static function getTypes() {
        return [
            ['id'=> Service::TYPE_NSI_CSP_2_0, 'name'=> Service::getTypeLabels()[Service::TYPE_NSI_CSP_2_0]],
        ];
    }
}




