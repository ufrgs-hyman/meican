<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use Yii;
use yii\web\Response;
use yii\data\ActiveDataProvider;

use meican\topology\models\Network;
use meican\topology\models\Domain;
use meican\topology\models\Port;
use meican\aaa\RbacController;

class ApiController extends RbacController {
	
	public function actionIndex() {
		$domains = Domain::find()->asArray()->all();
		$nets = Network::find()->asArray()->all();
		foreach ($nets as $net) {
			foreach ($domains as &$domain) {
				if ($domain['id'] == $net['domain_id'])
					$domain['networks'][] = $net;
			}
		}

		foreach ($domains as &$domain) {
			foreach ($domain['networks'] as &$net) {
				$ports = Port::find()
					->where(['directionality'=>'BI', 'network_id'=>$net['id']])
					->asArray()
					->all();
				$net['ports'] = $ports;
			}
		}
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		return $domains;
	}

}