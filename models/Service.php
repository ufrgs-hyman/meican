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
            case self::TYPE_NSI_DISCOVERY: return self::getTypeLabels()[self::TYPE_NSI_DISCOVERY];
            case self::TYPE_NSI_TOPOLOGY: return self::getTypeLabels()[self::TYPE_NSI_TOPOLOGY];
            case self::TYPE_NSI_CONNECTION: return self::getTypeLabels()[self::TYPE_NSI_CONNECTION];
            case self::TYPE_NMWG_TOPOLOGY: return self::getTypeLabels()[self::TYPE_NMWG_TOPOLOGY];
            case self::TYPE_NMWG_TOPO_PERFSONAR: return self::getTypeLabels()[self::TYPE_NMWG_TOPO_PERFSONAR];
            default: return Yii::t('topology', 'Unknown');
        }
    }

    static function getTypeLabels() {
        return [
            self::TYPE_NSI_DISCOVERY => Yii::t('topology', 'NSI Discovery Service'),
            self::TYPE_NSI_TOPOLOGY => Yii::t('topology', 'NSI Topology Description'),
            self::TYPE_NSI_CONNECTION => Yii::t('topology', 'NSI Connection Service'),
            self::TYPE_NMWG_TOPOLOGY => Yii::t('topology', 'NMWG Topology Description'),
            self::TYPE_NMWG_TOPO_PERFSONAR => Yii::t('topology', 'PerfSONAR Topology Service')
        ];
    }

    static function getTypes() {
        return [
            ['id'=> self::TYPE_NSI_DISCOVERY, 'name'=> self::getTypeLabels()[self::TYPE_NSI_DISCOVERY]],
            ['id'=> self::TYPE_NSI_TOPOLOGY, 'name'=> self::getTypeLabels()[self::TYPE_NSI_TOPOLOGY]],
            ['id'=> self::TYPE_NSI_CONNECTION, 'name'=> self::getTypeLabels()[self::TYPE_NSI_CONNECTION]],
            ['id'=> self::TYPE_NMWG_TOPOLOGY, 'name'=> self::getTypeLabels()[self::TYPE_NMWG_TOPOLOGY]],
            ['id'=> self::TYPE_NMWG_TOPO_PERFSONAR, 'name'=> self::getTypeLabels()[self::TYPE_NMWG_TOPO_PERFSONAR]]
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
