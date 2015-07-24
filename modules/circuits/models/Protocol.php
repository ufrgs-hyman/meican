<?php

namespace app\modules\circuits\models;

use Yii;

class Protocol {

    const TYPE_NSI_CS_2_0 = "nsi.cs.2.0";

    static function getTypes() {
        return [
            ['id'=> self::TYPE_NSI_CS_2_0, 'name'=> "NSI Connection Service 2.0"],
        ];
    }
}




