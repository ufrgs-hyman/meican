<?php

namespace app\modules\aaa\models;

use Yii;
use app\models\Preference;


class FederationPreference extends Preference {

	//retorna o Grupo em que os usuários vindos da federeção são adicionados
	const FEDERATION_GROUP = "aaa.federation.group";
	
    //retorna o Dominio para o qual usuários vindo da federeção tem seu perfil associados
    const FEDERATION_DOMAIN = "aaa.federation.domain";

    //retorna o Status da federação, se ela está ativa ou não
    const FEDERATION_STATUS = "aaa.federation.enabled";

    static function getNames() {
        return array_merge(parent::getNames(),[
            static::FEDERATION_GROUP,
            static::FEDERATION_DOMAIN,
            static::FEDERATION_STATUS,
        ]);
    }
}




