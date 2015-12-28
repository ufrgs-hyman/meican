<?php

namespace meican\circuits\models;

use Yii;

/**
 * This is the model class for table "{{%connection_path}}".
 *
 * @property integer $conn_id
 * @property integer $path_order
 * @property string $domain
 * @property string $port_urn
 * @property string $vlan
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
            [['conn_id', 'path_order', 'domain', 'port_urn', 'vlan'], 'required'],
            [['conn_id', 'path_order'], 'integer'],
            [['domain'], 'string', 'max' => 50],
            [['port_urn'], 'string', 'max' => 150],
            [['vlan'], 'string', 'max' => 20]
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
            'port_urn' => Yii::t('circuits', 'Src Urn'),
            'vlan' => Yii::t('circuits', 'Src Vlan'),
        ];
    }

    public function getConnection()
    {
        return $this->hasOne(Connection::className(), ['id' => 'conn_id']);
    }
    
    public function getPort() {
        return Port::findByUrn($this->port_urn);
    }

    public function getFullPortUrn() {
        return "urn:ogf:network:".$this->port_urn;
    }
    
    public function setPortBySTP($stp) {
    	$src = explode('?', $stp);
    	$this->port_urn = str_replace("urn:ogf:network:", "", $src[0]);
    	
    	$vlan = explode("=", $src[1]);
    	if (isset($vlan[1]))
    		$this->vlan = $vlan[1];
    	else {
    		$this->vlan = "";
    	}
    }
    
    public function setDomainBySTP($stp) {
    	$stp = explode('?', $stp);
    	$domain = explode(':', $stp[0]);
    	$this->domain = $domain[3];
    }
}
