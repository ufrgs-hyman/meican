<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%preference}}".
 *
 * @property string $name
 * @property string $value
 */
class Preference extends \yii\db\ActiveRecord
{
    //retorna o NSA que identifica a aplicação
    const MEICAN_NSA = "meican.nsa";

    //retorna o NSA do provedor padrão atualmente configurado.
    //o provedor pode não existir ou não ter o serviço necessário e
    //nesse caso um erro será gerado. 
    const CIRCUITS_DEFAULT_PROVIDER_NSA = "circuits.default.provider.nsa";

    //retorna um booleano que informa se as portas unidirecionais estão
    //disponíveis para novas reservas.
    const CIRCUITS_UNIPORT_ENABLED = "circuits.uniport.enabled";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%preference}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('circuits', 'Name'),
            'value' => Yii::t('circuits', 'Value'),
        ];
    }
}
