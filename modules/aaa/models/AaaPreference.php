<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\models;

use Yii;

use meican\base\models\Preference;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class AaaPreference extends Preference {

    //dominio padrao dos usuários logados via federação pela primeira vez
    const AAA_FEDERATION_DOMAIN = "aaa.federation.domain";

    //ativa o login via federação
    const AAA_FEDERATION_ENABLED = "aaa.federation.enabled";

    //grupo padrão dos usuários logados via federação pela primeira vez
    const AAA_FEDERATION_GROUP = "aaa.federation.group";

    static function getNames() {
        return array_merge(parent::getNames(),[
            static::AAA_FEDERATION_DOMAIN,
            static::AAA_FEDERATION_ENABLED,
            static::AAA_FEDERATION_GROUP,
        ]);
    }

    static function isFederationEnabled(){
        return (self::findOneValue(self::AAA_FEDERATION_ENABLED) === 'true');
    }
}




