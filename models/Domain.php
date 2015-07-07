<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "meican_domain".
 *
 * @property integer $id
 * @property string $name
 * @property string $default_policy
 *
 * @property BpmFlowControl[] $bpmFlowControls
 * @property BpmWorkflow[] $bpmWorkflows
 * @property Network[] $networks
 */
class Domain extends \yii\db\ActiveRecord {
	
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
				[['name', 'default_policy'], 'required'],
				[['default_policy'], 'string'],
				[['name'], 'string', 'max' => 60],
				[['name'], 'unique']
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
				'default_policy' => Yii::t('topology', 'Default Policy'),
		];
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
	public function getUserDomainsRoles()
	{
		return UserDomainRole::find()->where(['domain_id' => $this->id])->orWhere(['domain_id' => null]);
		//return $this->hasMany(UserDomainRole::className(), ['domain_id' => 'id']);
	}

    public function getOwnedWorkflows() {
    	return BpmWorkflow::find()->where(['domain_id'=>$this->id]);
    }
    
    public static function findByName($name) {
    	return Domain::find()->where(['name'=>$name]);
    }
}
