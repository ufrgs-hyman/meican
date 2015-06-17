<?php

namespace app\modules\topology\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use Yii;
use app\models\Domain;
use app\models\Provider;

class DomainFormModel extends Model {
	
	public $name;
	public $topology;
	public $oscars_version;
	public $isNewRecord = true;
	
	//provider
	public $nsa;
	public $connection_url;
	
	public $_provider;
	public $_domain;
	
	public $workflow_id;
	
	public $default_policy;
	
	public function rules()	{
		return [
			[['name', 'topology', 'oscars_version', 'default_policy'], 'required'],
			[['nsa', 'connection_url', 'workflow_id'], 'safe'],
		];
	}
	
	public function attributeLabels() {
		return [
			"name"=> Yii::t('topology', "Name"),
			"topology"=> Yii::t('topology', "Topology ID"),
			"nsa" => Yii::t('topology', "Bridge NSA ID"),
			"oscars_version" => Yii::t('topology', "Version"),
			"connection_url" => Yii::t('topology', "IDC URL"),
			"default_policy" => Yii::t('topology', "Default Policy")
		];
	}
	
	public function setFromRecord($domain) {
		$this->isNewRecord = false;
		$this->_provider = $domain->getProvider()->one();
		$this->_domain = $domain;
		$this->name = $domain->name;
		$this->topology = $domain->topology;
		$this->oscars_version = $domain->oscars_version;
		$this->nsa = $this->_provider->nsa;
		$this->connection_url = $this->_provider->connection_url;
		$this->default_policy = $domain->default_policy;
	}
	
	public function getProvider() {
		if (!$this->_provider) $this->_provider = new Provider;
		if ($this->nsa == "") { 
			$this->_provider->nsa = null;
		} else {
			$this->_provider->nsa = $this->nsa;
		}
		 
		$this->_provider->type = Domain::PROVIDER_TYPE;
		$this->_provider->connection_url = $this->connection_url;
		return $this->_provider;
	}
	
	public function getDomain() {
		if (!$this->_domain) $this->_domain = new Domain;
		$this->_domain->name = $this->name;
		$this->_domain->topology = $this->topology;
		$this->_domain->oscars_version = $this->oscars_version;
		$this->_domain->default_policy = $this->default_policy;
		
		return $this->_domain;
	}
	
	public function getPolicyOptions() {
		$options['ACCEPT_ALL'] = Yii::t("topology", 'Accept All');
		$options['REJECT_ALL'] = Yii::t("topology", 'Reject All');
		return $options;
	}
}
