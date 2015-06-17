<?php

namespace app\modules\topology\models;

use yii\base\Model;
use Yii;
use app\models\User;

class ImportForm extends Model {
	
	public $method = 0;
	public $url;
	public $otherUrl;
	public $xml;

	public function rules()	{
		return [
			[['method', 'url'], 'required'],
			[['otherUrl', 'xml'], 'safe']
		];
	}
	
	public function attributeLabels() {
		return [
			"method"=>Yii::t("topology", "Import method"),
			"url"=>Yii::t("topology", "Default URL"),
			"otherUrl" => Yii::t("topology", "Alternative URL"),
			"xml" => Yii::t("topology", "Optionally enter the XML (keep unfilled to import from the topology service)"),
		];
	}
	
	public function isUpdate() {
		return $this->method == "0";
	}
}
