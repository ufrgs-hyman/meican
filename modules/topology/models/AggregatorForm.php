<?php

namespace app\modules\topology\models;

use yii\base\Model;
use Yii;
use app\models\Aggregator;
use app\models\Provider;

class AggregatorForm extends Model {
	
	public $nsa;
	public $connection_url;
	public $discovery_url;
	
	public $_provider;
	
	public function rules()	{
		return [
			[['nsa', 'connection_url'], 'required'],
			[['discovery_url'], 'safe'],
		];
	}
	
	public function attributeLabels() {
		return [
			"nsa" => "NSA ID",
			"connection_url" => "Connection Service URL",
			"discovery_url" => "Discovery Service URL"
		];
	}
	
	public function setFromRecord($agg) {
		$this->_provider = $agg->getProvider()->one();
		$this->discovery_url = $this->_provider->discovery_url;
		$this->nsa = $this->_provider->nsa;
		$this->connection_url = $this->_provider->connection_url;
	}
	
	public function getProvider() {
		if (!$this->_provider) $this->_provider = new Provider;
		$this->_provider->nsa = $this->nsa;
		$this->_provider->type = Aggregator::PROVIDER_TYPE;
		$this->_provider->connection_url = $this->connection_url;
		$this->_provider->discovery_url = $this->discovery_url;
		return $this->_provider;
	}
}
