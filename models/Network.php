<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%network}}".
 *
 * @property integer $id
 * @property string $name
 * @property double $latitude
 * @property double $longitude
 * @property integer $domain_id
 *
 * @property Device[] $devices
 * @property Domain $domain
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
            [['name', 'domain_id'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['domain_id'], 'integer'],
            [['name'], 'string', 'max' => 60]
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
            'latitude' => Yii::t('topology', 'Latitude'),
            'longitude' => Yii::t('topology', 'Longitude'),
            'domain_id' => Yii::t('topology', 'Domain'),
        		
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
    public function getDevices() {
    	return $this->hasMany(Device::className(), ['network_id' => 'id']);
    }
}
