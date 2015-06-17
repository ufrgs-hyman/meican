<?php

namespace app\modules\topology\models;

use Yii;
use yii\base\Model;

class UrnForm extends Model {	
	
	public $id;
	public $value;
	public $port;
	public $max_capacity;
	public $min_capacity;
	public $granularity;
	public $device_id;
	public $device;
	public $network;
	public $vlans;
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
				'id' => Yii::t('topology', 'ID'),
				'value' => Yii::t('topology', 'Description'),
				'port' => Yii::t('topology', 'Port'),
				'max_capacity' => Yii::t('topology', 'Max Capacity (Mbps)'),
				'min_capacity' => Yii::t('topology', 'Min Capacity (Mbps)'),
				'granularity' => Yii::t('topology', 'Granularity (Mbps)'),
				'device' => Yii::t('topology', 'Device'),
		];
	}

}