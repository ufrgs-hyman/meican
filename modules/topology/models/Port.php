<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\models;

use Yii;

/**
 * This is the model class for table "{{%port}}".
 *
 * @property integer $id
 *
 * O tipo da porta identifica o formato da URN que a
 * representa. NMWG e NSI são suportados.
 *
 * @property string $type
 *
 * Portas podem ser bidirecionais, unidirecionais apenas de saída
 * ou unidirecionais apenas de entrada. 
 *
 * @property string $directionality
 * 
 * URN que representa a Porta de forma global. 
 * O valor persistido não deve conter o prefixo 
 * standard da OGF: 'urn:ogf:network:'. Ele é inserido
 * quando mostrado ao usuário ou enviado a agentes externos.
 *
 * @property string $urn
 * @property string $name
 * @property integer $max_capacity
 * @property integer $min_capacity
 * @property integer $granularity
 * @property string $vlan_range
 *
 * Portas unidirecionais do tipo NSI devem ter associadas
 * portas bidirecionais.
 *
 * @property integer $biport_id
 *
 * Alias é o nome dado a uma porta com a qual 
 * existe um Link.
 *
 * @property integer $alias_id
 *
 * Toda porta deve possuir um Device (Nodo) associado.
 * Esse device pode não ter nome representativo, mas
 * deve existir para a correta aglomeração no mapa.
 *
 * @property integer $device_id
 *
 * Apenas portas do tipo NSI possuem redes associadas, 
 * uma vez que essas redes só existem na topologia NSI.
 *
 * @property integer $network_id
 *
 * @property Port $biport
 * @property Port[] $ports
 * @property Device $device
 * @property Network $network
 * @property Port $alias
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Port extends \yii\db\ActiveRecord
{
    const TYPE_NMWG = "NMWG";
    const TYPE_NSI = "NSI";

    //directionality
    const DIR_BI = "BI";
    const DIR_UNI_IN = "UNI_IN";
    const DIR_UNI_OUT = "UNI_OUT";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%port}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'directionality', 'urn', 'name'], 'required'],
            [['type', 'directionality'], 'string'],
            [['vlan_range'], 'string'],
            [['max_capacity', 'min_capacity', 'capacity', 'granularity', 'biport_id', 'alias_id', 'network_id', 'location_id'], 'integer'],
            [['urn'], 'string', 'max' => 250],
            [['name'], 'string', 'max' => 100],
            [['urn'], 'unique'],
        	['vlan_range', 'validateVlan'],
        ];
    }

    public function validateVlan($attribute, $params)
    {
    	$this->$attribute;
    	
    	$vlans = explode(",", $this->$attribute);
    	foreach($vlans as $vlan){
    		if($vlan == "") $this->addError($attribute, Yii::t('topology', 'Sintax error. Samples: "200" or "200-300" or "200-300,800-990"'));
    		$elements = str_split($vlan);
    		foreach($elements as $element){
    			if(is_numeric($element) || $element == '-'){
    				if($element == '-' && $element === end($elements)) $this->addError($attribute, Yii::t('topology', 'Sintax error. Samples: "200" or "200-300" or "200-300,800-990"'));
    			}
    			else $this->addError($attribute, Yii::t('topology', 'Sintax error. Samples: "200" or "200-300" or "200-300,800-990"'));
    		}
    	}
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('topology', 'ID'),
            'type' => Yii::t('topology', 'Type'),
            'directionality' => Yii::t('topology', 'Directionality'),
            'urn' => Yii::t('topology', 'URN'),
            'name' => Yii::t('topology', 'Name'),
            'capacity' => Yii::t('topology', 'Capacity (Mbps)'),
            'max_capacity' => Yii::t('topology', 'Max Reservable Capacity (Mbps)'),
            'min_capacity' => Yii::t('topology', 'Min Reservable Capacity (Mbps)'),
            'granularity' => Yii::t('topology', 'Granularity (Mbps)'),
            'biport_id' => Yii::t('topology', 'Biport ID'),
            'alias_id' => Yii::t('topology', 'Alias ID'),
            'network_id' => Yii::t('topology', 'Network'),
        	'vlan_range' => Yii::t('topology', 'VLANs'),
            'location_id' => Yii::t('topology', 'Location'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBiPort()
    {
        return $this->hasOne(Port::className(), ['id' => 'biport_id']);
    }

    public function getUniPorts() {
        return $this->hasMany(Port::className(), ['biport_id'=> 'id']);
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
    public function getLocation()
    {
        return $this->hasMany(Location::className(), ['id' => 'location_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlias()
    {
        return $this->hasOne(Port::className(), ['id' => 'alias_id']);
    }

    static function findOneArraySelect($id, $array) {
        return self::find()->where(['id'=>$id])->asArray()->select($array)->one();
    }

    static function findByUrn($urn) {
        return self::find()->where(['urn'=>$urn]);
    }

    static function findByName($name) {
        return self::find()->where(['name'=>$name]);
    }

    static function findOneByUrn($urn) {
        return self::find()->where(['urn'=>$urn])->one();
    }
    
    public function setAlias($port) {
        $this->alias_id = $port->id;
    }
    
    public function getFullUrn() {
        return "urn:ogf:network:".$this->urn;
    }

    public function getInboundPortVlanRange() {
        $inboundPort = $this->getUniPorts()->andWhere(['directionality'=>self::DIR_UNI_IN])->select(['vlan_range'])->one();
        if ($inboundPort) return $inboundPort->vlan_range;
        return null;
    }
}
