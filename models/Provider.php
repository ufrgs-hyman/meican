<?php

namespace meican\models;

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
            [['type', 'name', 'nsa','domain_id'], 'required'],
            [['type'], 'string'],
            [['domain_id'], 'integer'],
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
            'id' => Yii::t('topology', 'ID'),
            'type' => Yii::t('topology', 'Type'),
            'name' => Yii::t('topology', 'Name'),
            'nsa' => Yii::t('topology', 'NSA ID'),
            'latitude' => Yii::t('topology', 'Latitude'),
            'longitude' => Yii::t('topology', 'Longitude'),
            'domain_id' => Yii::t("topology", 'Domain'),
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
        ];
    }

    public function getType() {
        switch ($this->type) {
            case self::TYPE_AGG: return Yii::t('circuits', 'Aggregator');
            case self::TYPE_UPA: return Yii::t('circuits', 'Ultimate (uPA)');
            default: return Yii::t('topology', 'Unknown');
        }
    }

    public function getTypeLabels() {
        return [
            self::TYPE_AGG => Yii::t('circuits', 'Aggregator'),
            self::TYPE_UPA =>Yii::t('circuits', 'Ultimate (uPA)'),
        ];
    }

    public function getServices() {
        return $this->hasMany(Service::className(), ['provider_id' => 'id']);
    }

    public function getConnectionService() {
        return $this->getServices()->andWhere(['type'=>Service::TYPE_NSI_CSP_2_0]);
    }

    static function findByNsa($nsa) {
        return self::find()->where(['nsa'=>$nsa]);
    }

    static function findOneByNsa($nsa) {
        return self::find()->where(['nsa'=>$nsa])->one();
    }

    public function isDummy() {
        return Yii::$app->params["provider.force.dummy"];
    }

    public function getDomainName() {
        return Domain::findOne($this->domain_id)->name;
    }

    public function getPeerings() {
        return $this->hasMany(ProviderPeering::className(), ['provider_id' => 'id']);
    }
}
