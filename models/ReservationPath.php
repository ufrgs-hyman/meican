<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reservation_path}}".
 *
 * @property integer $reservation_id
 * @property integer $path_order
 * 
 * @property string $urn
 *  
 * @property string $domain
 * @property string $device
 * @property string $port
 * @property string $vlan
 *
 * @property Reservation $reservation
 */
class ReservationPath extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reservation_path}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reservation_id', 'path_order', 'urn','domain', 'device', 'vlan'], 'required'],
            [['reservation_id', 'path_order'], 'integer'],
            [['urn'], 'string', 'max' => 150],
            [['domain', 'device', 'port'], 'string', 'max' => 50],
            [['vlan'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reservation_id' => Yii::t('circuits', 'Reservation ID'),
            'path_order' => Yii::t('circuits', 'Path Order'),
            'domain' => Yii::t('circuits', 'Domain'),
            'device' => Yii::t('circuits', 'Device'),
            'port' => Yii::t('circuits', 'Port'),
            'vlan' => Yii::t('circuits', 'Vlan'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservation() {
        return $this->hasOne(Reservation::className(), ['id' => 'reservation_id']);
    }
    
    public function getUrn() {
    	return Urn::findByValue($this->getUrnValue());
    }
    
    public function getUrnValue() {
    	return "urn:ogf:network:".$this->urn;
    }
    
    public function setUrn($urn) {
    	$dev = $urn->getDevice()->one();
    	$net = $dev->getNetwork()->one();
    	$dom = $net->getDomain()->one();
    	$this->domain = $dom->topology;
    	$this->device = $dev->node;
    	$this->port = $urn->port;
    	$this->urn = str_replace("urn:ogf:network:", "", $urn->value);
    }
}
