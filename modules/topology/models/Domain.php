<?php
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\models;

use Yii;
use meican\aaa\models\UserDomainRole;
use meican\bpm\models\BpmWorkflow;

/**
 * This is the model class for table "meican_domain".
 *
 * @property integer $id
 * @property string $name
 * @property integer $graph_x
 * @property integer $graph_y
 * @property string $color
 * @property string $default_policy
 *
 * @property Device[] $devices
 * @property Network[] $networks
 * @property UserDomain[] $userDomains
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Domain extends \yii\db\ActiveRecord {
	
	const ACCEPT_ALL = "ACCEPT_ALL";
	const REJECT_ALL = "REJECT_ALL";
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meican_domain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'color'], 'required'],
            [['default_policy'], 'string'],
            [['name'], 'string', 'max' => 60],
            [['color'], 'string', 'max' => 10],
            [['grouped_nodes'],'boolean'],
            [['graph_x', 'graph_y'], 'integer'],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('topology', 'Name'),
            'color' => Yii::t('topology', 'Color'),
            'grouped_nodes' => Yii::t('topology', 'Grouped Nodes'),
            'default_policy' => Yii::t('topology', 'Default Policy'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['domain_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworks()
    {
        return $this->hasMany(Network::className(), ['domain_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDomains()
    {
        return $this->hasMany(UserDomain::className(), ['domain_id' => 'id']);
    }

    public function getOwnedWorkflows() {
    	return BpmWorkflow::find()->where(['domain_id'=>$this->id]);
    }
    
    public static function findByName($name) {
    	return Domain::find()->where(['name'=>$name]);
    }

    static function findOneByName($name) {
        return Domain::find()->where(['name'=>$name])->one();
    }
    
    public function getPolicy(){
    	if($this->default_policy == self::ACCEPT_ALL) return Yii::t('topology', 'Accept All');
    	if($this->default_policy == self::REJECT_ALL) return Yii::t('topology', 'Reject All');
    }
    
    public function getPolicyOptions() {
    	$options[self::ACCEPT_ALL] = Yii::t("topology", 'Accept All');
    	$options[self::REJECT_ALL] = Yii::t("topology", 'Reject All');
    	return $options;
    }
    
    public function getUserDomainsRoles()
    {
    	return UserDomainRole::find()->where(['domain' => $this->name])->orWhere(['domain' => null]);
    }
    
    public function getBiPorts(){
    	$portsArray = [];
    	
    	$networks = Network::find()->where(['domain_id' => $this->id])->all();
    	
    	foreach($networks as $network){
    		$ports = Port::find()->where(['network_id' => $network->id, 'type' => Port::TYPE_NSI, 'directionality' => Port::DIR_BI])->orderBy(['name' => SORT_ASC])->all();
    		foreach($ports as $port){
    			$portsArray[$port->id] = $port;
    		}
    	}
    	return $portsArray;  	
    }
        
}
