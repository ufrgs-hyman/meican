<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%device}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $ip
 * @property string $trademark
 * @property string $model
 * @property double $latitude
 * @property double $longitude
 * @property string $node
 * @property integer $network_id
 *
 * @property Network $network
 * @property Urn[] $urns
 */
class Device extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%device}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'network_id','node'], 'required'],
            [['network_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['name', 'trademark', 'model', 'node'], 'string', 'max' => 50],
            [['ip'], 'string', 'max' => 16],
            [['node'], 'unique', 'targetAttribute' => ['node','network_id'], 'message' => 'The selected Network has already a Device with the same Node name.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t("topology",'ID'),
            'name' => Yii::t("topology",'Name'),
            'ip' => Yii::t("topology",'IP'),
            'trademark' => Yii::t("topology",'Trademark'),
            'model' => Yii::t("topology",'Model'),
            'latitude' => Yii::t("topology",'Latitude'),
            'longitude' => Yii::t("topology",'Longitude'),
            'node' => Yii::t("topology",'Node'),
            'network_id' => Yii::t("topology",'Network'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::className(), ['id' => 'network_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUrns()
    {
        return $this->hasMany(Urn::className(), ['device_id' => 'id']);
    }
}
