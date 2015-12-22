<?php

namespace meican\models;

use Yii;

/**
 * This is the model class for table "{{%network}}".
 *
 * @property integer $id
 * @property string $name
 *
 * Identificador globalmente único da rede.
 * O valor persistido não deve conter o prefixo 
 * standard da OGF: 'urn:ogf:network:'. Ele é inserido
 * quando mostrado ao usuário ou enviado a agentes externos.
 *
 * @property string $urn
 *
 * Localização aproximada
 *
 * @property string $address
 *
 * Coordenadas relativas a localização aproximada
 *
 * @property double $latitude
 * @property double $longitude
 * @property integer $domain_id
 *
 * @property Domain $domain
 * @property Port[] $ports
 */
class Network extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%network}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'urn', 'domain_id'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['domain_id'], 'integer'],
            [['address'], 'string', 'max' => 200],
            [['name'], 'string', 'max' => 60],
            [['urn'], 'string', 'max' => 250],
            [['urn'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('topology', 'ID'),
            'name' => Yii::t('topology', 'Name'),
            'urn' => Yii::t('topology', 'Urn'),
            'latitude' => Yii::t('topology', 'Latitude'),
            'longitude' => Yii::t('topology', 'Longitude'),
            'domain_id' => Yii::t('topology', 'Domain ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPorts()
    {
        return $this->hasMany(Port::className(), ['network_id' => 'id']);
    }

    static function findByUrn($urn) {
        return self::find()->where(['urn'=>$urn]);
    }
}
