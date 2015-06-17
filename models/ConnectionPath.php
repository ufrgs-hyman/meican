<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%connection_path}}".
 *
 * @property integer $conn_id
 * @property integer $path_order
 * @property string $domain
 * @property string $src_urn
 * @property string $src_vlan
 * @property string $dst_urn
 * @property string $dst_vlan
 *
 * @property Connection $conn
 */
class ConnectionPath extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%connection_path}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['conn_id', 'path_order', 'domain','src_urn', 'src_vlan', 'dst_urn', 'dst_vlan'], 'required'],
            [['conn_id', 'path_order'], 'integer'],
            [['domain'], 'string', 'max' => 50],
            [['src_urn', 'dst_urn'], 'string', 'max' => 150],
            [['src_vlan', 'dst_vlan'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'conn_id' => Yii::t('circuits', 'Conn ID'),
            'path_order' => Yii::t('circuits', 'Path Order'),
            'src_urn' => Yii::t('circuits', 'Src Urn'),
            'src_vlan' => Yii::t('circuits', 'Src Vlan'),
            'dst_urn' => Yii::t('circuits', 'Dst Urn'),
            'dst_vlan' => Yii::t('circuits', 'Dst Vlan'),
        ];
    }

    public function getConnection()
    {
        return $this->hasOne(Connection::className(), ['id' => 'conn_id']);
    }
    
    public function getDestinationUrn() {
    	return Urn::findByValue(self::toUrnValue($this->dst_urn));
    }
    
    public function getSourceUrn() {
    	return Urn::findByValue(self::toUrnValue($this->src_urn));
    }
    
    public function setSourceUrn($value) {
    	$this->src_urn = str_replace("urn:ogf:network:", "", $value);
    }
    
    public function setDestinationUrn($value) {
    	$this->dst_urn = str_replace("urn:ogf:network:", "", $value);
    }
    
    public function setSourceStp($stp) {
    	$src = explode('?', $stp);
    	$this->src_urn = str_replace("urn:ogf:network:", "", $src[0]);
    	
    	$vlan = explode("=", $src[1]);
    	if (isset($vlan[1]))
    		$this->src_vlan = $vlan[1];
    	else {
    		$this->src_vlan = "";
    	}
    }
    
    public function setDestinationStp($stp) {
    	$dst = explode('?', $stp);
    	$this->dst_urn = str_replace("urn:ogf:network:", "", $dst[0]);
    	
    	$vlan = explode("=", $dst[1]);
    	if (isset($vlan[1]))
    		$this->dst_vlan = $vlan[1];
    	else {
    		$this->dst_vlan = "";
    	}
    }
    
    static function toUrnValue($partialValue) {
    	return "urn:ogf:network:".$partialValue;
    }
    
    public function setDomainByStp($stp) {
    	$stp = explode('?', $stp);
    	$domain = explode(':', $stp[0]);
    	$this->domain = $domain[3];
    }
}
