<?php

namespace app\modules\circuits\models;

use Yii;
use app\models\Preference;

class CircuitsPreference extends Preference {

    //retorna o NSA do provedor padrão atualmente configurado.
    const CIRCUITS_DEFAULT_PROVIDER_NSA = "circuits.default.provider.nsa";

    //retorna a URL do serviço de conexão padrão atualmente configurado.
    const CIRCUITS_DEFAULT_CS_URL = "circuits.default.cs.url";

    //retorna um booleano que informa se as portas unidirecionais estão
    //disponíveis para novas reservas.
    const CIRCUITS_UNIPORT_ENABLED = "circuits.uniport.enabled";

    static function getNames() {
        return array_merge(parent::getNames(),[
            static::CIRCUITS_UNIPORT_ENABLED,
            static::CIRCUITS_DEFAULT_PROVIDER_NSA,
            static::CIRCUITS_DEFAULT_CS_URL
        ]);
    }
}




