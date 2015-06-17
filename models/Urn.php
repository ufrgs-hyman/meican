<?php

namespace app\models;

use Yii;
use app\models\VlanRange;

/**
 * This is the model class for table "{{%urn}}".
 *
 * @property integer $id
 * @property string $value
 * @property string $port
 * @property integer $max_capacity
 * @property integer $min_capacity
 * @property integer $granularity
 * @property integer $device_id
 *
 * @property FlowPath[] $flowPaths
 * @property Device $device
 * @property VlanRange[] $vlanRanges
 */
class Urn extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%urn}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value', 'device_id'], 'required'],
            [['max_capacity', 'min_capacity', 'granularity', 'alias_urn_id', 'device_id'], 'integer'],
            [['value'], 'string', 'max' => 150],
            [['port'], 'string', 'max' => 50],
            [['value'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('topology', 'ID'),
            'value' => Yii::t('topology', 'Value'),
            'port' => Yii::t('topology', 'Port'),
            'max_capacity' => Yii::t('topology', 'Max Capacity'),
            'min_capacity' => Yii::t('topology', 'Min Capacity'),
            'granularity' => Yii::t('topology', 'Granularity'),
            'device_id' => Yii::t('topology', 'Device'),
        ];
    }
    
    public function getDomain() {
    	return $this->getDevice()->one()->getNetwork()->one()->getDomain();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVlanRanges()
    {
        return $this->hasMany(VlanRange::className(), ['urn_id' => 'id']);
    }
    
    public function updateVlans($vlanRanges) {
    	$this->removeVlans();
    	 
    	$rangesArray = explode(",", $vlanRanges);
    	foreach ($rangesArray as $range) {
    			$vlan = new VlanRange;
    			$vlan->value = $range;
    			$vlan->urn_id = $this->id;
    			if(!$vlan->save()) {
    				Yii::trace("Erro ao salvar vlan range");
    			}
    	}
    }
    
    public function removeVlans() {
    	$urnVlans = VlanRange::findAll(['urn_id' => $this->id]);
    	foreach ($urnVlans as $urnVlan) {
    		$urnVlan->delete();
    	}
    }
    
    public static function findByValue($value) {
    	return self::find()->where(['value'=>$value]);
    }
    
    public function setAlias($urn) {
    	$this->alias_urn_id = $urn->id;
    }
    
    public function getAlias() {
    	return self::find()->where(['id' => $this->alias_urn_id]);
    }
}
