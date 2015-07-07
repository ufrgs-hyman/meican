<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%vlan_range}}".
 *
 * @property integer $id
 * @property string $value
 * @property integer $port_id
 *
 * @property Port $port
 */
class VlanRange extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vlan_range}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value', 'port_id'], 'required'],
            [['port_id'], 'integer'],
            [['value'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => Yii::t('topology', 'Value'),
            'port_id' => Yii::t('topology', 'Port ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUrn()
    {
        return $this->hasOne(Urn::className(), ['id' => 'port_id']);
    }
    
    public function getValidVlan() {
    	$interval = explode("-", $this->value);
    	if (count($interval) < 2) {
    		return $this->value;
    	} else {
    		return $interval[0];
    	}
    }
}
