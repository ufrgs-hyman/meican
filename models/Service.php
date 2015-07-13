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
    const TYPE_NSI_DISCOVERY = "NSI_DISCOVERY";
    const TYPE_NSI_TOPOLOGY = "NSI_TOPOLOGY";
    const TYPE_NSI_CONNECTION = "NSI_CONNECTION";
    const TYPE_NMWG_TOPOLOGY = 'NMWG_TOPOLOGY';
    const TYPE_NMWG_TOPO_PERFSONAR = 'NMWG_TOPO_PERFSONAR';

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
            case self::TYPE_NSI_DISCOVERY: return Yii::t('topology', 'NSI Discovery Service');
            case self::TYPE_NSI_TOPOLOGY: return Yii::t('topology', 'NSI Topology Description');
            case self::TYPE_NSI_CONNECTION: return Yii::t('topology', 'NSI Connection Service');
            case self::TYPE_NMWG_TOPOLOGY: return Yii::t('topology', 'NMWG Topology Description');
            case self::TYPE_NMWG_TOPO_PERFSONAR: return Yii::t('topology', 'PerfSONAR Topology Service');
            default: return Yii::t('topology', 'Unknown');
        }
    }

    static function getTypes() {
        return [
            ['id'=> self::TYPE_NSI_DISCOVERY, 'name'=> Yii::t('topology', 'NSI Discovery Service')],
            ['id'=> self::TYPE_NSI_TOPOLOGY, 'name'=> Yii::t('topology', 'NSI Topology Description')],
            ['id'=> self::TYPE_NSI_CONNECTION, 'name'=> Yii::t('topology', 'NSI Connection Service')],
            ['id'=> self::TYPE_NMWG_TOPOLOGY, 'name'=> Yii::t('topology', 'NMWG Topology Description')],
            ['id'=> self::TYPE_NMWG_TOPO_PERFSONAR, 'name'=> Yii::t('topology', 'PerfSONAR Topology Service')]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['id' => 'provider_id']);
    }
}
