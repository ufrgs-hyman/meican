<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\models;

use Yii;

use meican\base\models\Preference;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class CircuitsPreference extends Preference {

    //retorna o NSA do provedor padrão atualmente configurado.
    const CIRCUITS_DEFAULT_PROVIDER_NSA = "circuits.default.provider.nsa";

    //retorna a URL do serviço de conexão padrão atualmente configurado.
    const CIRCUITS_DEFAULT_CS_URL = "circuits.default.cs.url";

    //URL do requester do MEICAN (automaticamente definida, não deve ser alterada)
    const CIRCUITS_MEICAN_REQUESTER_URL = "circuits.meican.requester.url";

    //retorna um booleano que informa se as portas unidirecionais estão
    //disponíveis para novas reservas.
    const CIRCUITS_UNIPORT_ENABLED = "circuits.uniport.enabled";

    const CIRCUITS_PROTOCOL = "circuits.protocol";

    static function getNames() {
        return array_merge(parent::getNames(),[
            static::CIRCUITS_UNIPORT_ENABLED,
            static::CIRCUITS_DEFAULT_PROVIDER_NSA,
            static::CIRCUITS_DEFAULT_CS_URL,
            static::CIRCUITS_MEICAN_REQUESTER_URL,
            static::CIRCUITS_PROTOCOL,
        ]);
    }
}




