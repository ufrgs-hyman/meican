<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "meican_domain".
 *
 * @property integer $id
 * @property string $name
 * @property string $topology
 * @property string $oscars_version
 * @property integer $workflow_id
 * @property string $default_policy
 *
 * @property BpmFlowControl[] $bpmFlowControls
 * @property BpmWorkflow[] $bpmWorkflows
 * @property ConnectionAuth[] $connectionAuths
 * @property Provider $id0
 * @property DomainAggregator[] $domainAggregators
 * @property Aggregator[] $aggregators
 * @property Network[] $networks
 * @property ReservationPath[] $reservationPaths
 * @property UserDomain[] $userDomains
 */
class Domain extends \yii\db\ActiveRecord
{
	
	const PROVIDER_TYPE = Provider::TYPE_BRIDGE;
	
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
				[['id', 'name', 'topology', 'oscars_version'], 'required'],
				[['id'], 'integer'],
				[['oscars_version', 'default_policy'], 'string'],
				[['name'], 'string', 'max' => 30],
				[['topology'], 'string', 'max' => 50],
				[['topology'], 'unique']
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
				'topology' => Yii::t('topology', 'Topology'),
				'oscars_version' => Yii::t('topology', 'OSCARS Version'),
				'default_policy' => Yii::t('topology', 'Default Policy'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBpmFlowControls()
	{
		return $this->hasMany(BpmFlowControl::className(), ['domain_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBpmWorkflows()
	{
		return $this->hasMany(BpmWorkflow::className(), ['domain_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getConnectionAuths()
	{
		return $this->hasMany(ConnectionAuth::className(), ['domain_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getId0()
	{
		return $this->hasOne(Provider::className(), ['id' => 'id']);
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
	public function getReservationPaths()
	{
		return $this->hasMany(ReservationPath::className(), ['domain_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUserDomainsRoles()
	{
		return UserDomainRole::find()->where(['domain_id' => $this->id])->orWhere(['domain_id' => null]);
		//return $this->hasMany(UserDomainRole::className(), ['domain_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProvider()
	{
		return $this->hasOne(Provider::className(), ['id' => 'id']);
	}
	
	public function setProvider($provider) {
		$this->id = $provider->id;
	}

    public function delete() {
    	return Provider::deleteAll(['id'=>$this->id]);
    }
    
    public static function getDefaultVersion() {
    	return "0.6";
    }
    
    public static function getAllVersions() {
    	return ["0.6"];
    }
    
    public function getOwnedWorkflows() {
    	return BpmWorkflow::find()->where(['domain_id'=>$this->id]);
    }
    
    public static function findByTopology($topo) {
    	return Domain::find()->where(['topology'=>$topo]);
    }
}
