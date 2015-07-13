<?php

namespace app\modules\topology\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use Yii;
use app\models\Domain;
use app\models\Provider;

class DomainFormModel extends Model {
	
	public $name;
	public $isNewRecord = true;

	public $_domain;
	
	public $default_policy;
	
	public function rules()	{
		return [
			[['name', 'default_policy'], 'required'],
		];
	}
	
	public function attributeLabels() {
		return [
			"name"=> Yii::t('topology', "Name"),
			"default_policy" => Yii::t('topology', "Default Policy")
		];
	}
	
	public function setFromRecord($domain) {
		$this->isNewRecord = false;
		$this->_domain = $domain;
		$this->name = $domain->name;
		$this->default_policy = $domain->default_policy;
	}

	
	public function getDomain() {
		if (!$this->_domain) $this->_domain = new Domain;
		$this->_domain->name = $this->name;
		$this->_domain->default_policy = $this->default_policy;
		
		return $this->_domain;
	}
	
	public function getPolicyOptions() {
		$options['ACCEPT_ALL'] = Yii::t("topology", 'Accept All');
		$options['REJECT_ALL'] = Yii::t("topology", 'Reject All');
		return $options;
	}
}
