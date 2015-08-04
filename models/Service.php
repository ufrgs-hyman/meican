<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%service}}".
 *
 * @property integer $id
 * @property integer $provider_id
 * @property string $type
 * @property string $url
 *
 * @property Provider $provider
 */
class Service extends \yii\db\ActiveRecord
{
    const TYPE_NSI_DS_1_0 = "NSI_DS_1_0";
    const TYPE_NSI_TD_2_0 = "NSI_TD_2_0";
    const TYPE_NSI_CSP_2_0 = "NSI_CSP_2_0";
    const TYPE_NMWG_TD_1_0 = 'NMWG_TD_1_0';
    const TYPE_PERFSONAR_TS_1_0 = 'PERFSONAR_TS_1_0';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provider_id', 'type', 'url'], 'required'],
            [['provider_id'], 'integer'],
            [['type'], 'string'],
            [['url'], 'string', 'max' => 2000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('circuits', 'ID'),
            'provider_id' => Yii::t('circuits', 'Provider ID'),
            'type' => Yii::t('circuits', 'Type'),
            'url' => Yii::t('circuits', 'URL'),
        ];
    }

    public function getType() {
        switch ($this->type) {
            case self::TYPE_NSI_DS_1_0: return self::getTypeLabels()[self::TYPE_NSI_DS_1_0];
            case self::TYPE_NSI_TD_2_0: return self::getTypeLabels()[self::TYPE_NSI_TD_2_0];
            case self::TYPE_NSI_CSP_2_0: return self::getTypeLabels()[self::TYPE_NSI_CSP_2_0];
            case self::TYPE_NMWG_TD_1_0: return self::getTypeLabels()[self::TYPE_NMWG_TD_1_0];
            case self::TYPE_PERFSONAR_TS_1_0: return self::getTypeLabels()[self::TYPE_PERFSONAR_TS_1_0];
            default: return Yii::t('topology', 'Unknown');
        }
    }

    static function getTypeLabels() {
        return [
            self::TYPE_NSI_DS_1_0 => Yii::t('topology', 'NSI Discovery Service 1.0'),
            self::TYPE_NSI_TD_2_0 => Yii::t('topology', 'NSI Topology Description 2.0'),
            self::TYPE_NSI_CSP_2_0 => Yii::t('topology', 'NSI Connection Service 2.0'),
            self::TYPE_NMWG_TD_1_0 => Yii::t('topology', 'NMWG Topology Description 1.0'),
            self::TYPE_PERFSONAR_TS_1_0 => Yii::t('topology', 'PerfSONAR Topology Service 1.0')
        ];
    }

    static function getTypes() {
        return [
            ['id'=> self::TYPE_NSI_DS_1_0, 'name'=> self::getTypeLabels()[self::TYPE_NSI_DS_1_0]],
            ['id'=> self::TYPE_NSI_TD_2_0, 'name'=> self::getTypeLabels()[self::TYPE_NSI_TD_2_0]],
            ['id'=> self::TYPE_NSI_CSP_2_0, 'name'=> self::getTypeLabels()[self::TYPE_NSI_CSP_2_0]],
            ['id'=> self::TYPE_NMWG_TD_1_0, 'name'=> self::getTypeLabels()[self::TYPE_NMWG_TD_1_0]],
            ['id'=> self::TYPE_PERFSONAR_TS_1_0, 'name'=> self::getTypeLabels()[self::TYPE_PERFSONAR_TS_1_0]]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['id' => 'provider_id']);
    }

    static function findOneByUrl($url) {
        return self::find()->where(['url'=>$url])->one();
    }
}
