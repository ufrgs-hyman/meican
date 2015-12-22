<?php

namespace meican\modules\circuits\models;

use Yii;
use meican\models\Service;

class Protocol {

    static function getTypes() {
        return [
            ['id'=> Service::TYPE_NSI_CSP_2_0, 'name'=> Service::getTypeLabels()[Service::TYPE_NSI_CSP_2_0]],
        ];
    }
}




