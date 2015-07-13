<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%provider}}".
 *
 * @property integer $id
 * @property string $type
 * @property string $name
 *
 * Identificador globalmente único do provedor.
 * O valor persistido não deve conter o prefixo 
 * standard da OGF: 'urn:ogf:network:'. Ele é inserido
 * quando mostrado ao usuário ou enviado a agentes externos.
 *
 * @property string $nsa
 * @property double $latitude
 * @property double $longitude
 *
 * @property Reservation[] $reservations
 * @property Service[] $services
 */
class Provider extends \yii\db\ActiveRecord
{
    const TYPE_DUMMY = "DUMMY";
    const TYPE_UPA = "UPA";
    const TYPE_AGG = "AGG";
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%provider}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'name', 'nsa'], 'required'],
            [['type'], 'string'],
            [['latitude', 'longitude'], 'number'],
            [['name'], 'string', 'max' => 100],
            [['nsa'], 'string', 'max' => 200],
            [['nsa'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('circuits', 'ID'),
            'type' => Yii::t('circuits', 'Type'),
            'name' => Yii::t('circuits', 'Name'),
            'nsa' => Yii::t('circuits', 'NSA ID'),
            'latitude' => Yii::t('circuits', 'Latitude'),
            'longitude' => Yii::t('circuits', 'Longitude'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservations()
    {
        return $this->hasMany(Reservation::className(), ['provider_id' => 'id']);
    }

    static function getTypes() {
        return [
            ['id'=>self::TYPE_AGG, 'name'=>Yii::t('circuits', 'Aggregator')],
            ['id'=>self::TYPE_UPA, 'name'=>Yii::t('circuits', 'Ultimate (uPA)')],
            ['id'=>self::TYPE_DUMMY, 'name'=>Yii::t('circuits', 'Dummy')],
        ];
    }

    public function getType() {
        switch ($this->type) {
            case self::TYPE_AGG: return Yii::t('circuits', 'Aggregator');
            case self::TYPE_UPA: return Yii::t('circuits', 'Ultimate (uPA)');
            case self::TYPE_DUMMY: return Yii::t('circuits', 'Dummy');
            default: return Yii::t('topology', 'Unknown');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::className(), ['provider_id' => 'id']);
    }

    public function getConnectionService() {
        return $this->getServices()->andWhere(['type'=>Service::TYPE_NSI_CONNECTION]);
    }

    static function findByNsa($nsa) {
        return self::find()->where(['nsa'=>$nsa]);
    }

    public function isDummy() {
        return Yii::$app->params["provider.force.dummy"] || ($this->type == self::TYPE_DUMMY);
    }
}
