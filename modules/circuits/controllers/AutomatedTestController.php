<?php

namespace app\modules\circuits\controllers;

use yii\web\Controller;
use yii\helpers\Url;
use app\controllers\RbacController;
use Yii;
use app\models\Reservation;
use app\models\Domain;
use app\models\Cron;
use yii\data\ActiveDataProvider;
use app\modules\circuits\models\CircuitsPreference;
use app\modules\circuits\models\AutomatedTestForm;
use app\modules\circuits\models\AutomatedTest;

class AutomatedTestController extends RbacController {

	public $enableCsrfValidation = false;
	
	public function actionIndex($mode = "read") {
		$data = new ActiveDataProvider([
    		'query' => AutomatedTest::find()->where(['type'=> Reservation::TYPE_TEST]),
    		'sort' => false,
    	]);
			
		return $this->render('/tests/status', array(
			'data' => $data,
			'mode' => $mode,
			'domains' => json_encode(Domain::find()->orderBy(['name'=> "SORT ASC"])->asArray()->select(['id','name'])->all()),
		));
	}
	
	public function actionCreate() {
		if(Yii::$app->request->isAjax) {
			$form = new AutomatedTestForm;
			if ($form->load($_POST)) {
				if ($form->validate() && $form->save()) {
					$this->checkRequesterUrl();
					return true;
				}
			}
			 
			return false;
		} else {
			return $this->actionIndex("create");
		}
	}

	private function checkRequesterUrl() {
		$pref = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_MEICAN_REQUESTER_URL);
		if ($pref) {
			$url = Url::toRoute("/circuits/requester", "http");
			if ($pref->value != $url) {
				$pref->value = $url;
				$pref->save();
			}
		}
	}
	
	public function actionUpdate($id) {
		$form = new AutomatedTestForm;
		if ($form->load($_POST)) {
			$cron = Cron::findOneTestTask($id);
			$cron->freq = $form->cron_value;
			$cron->status = Cron::STATUS_PROCESSING;
			if ($cron->save()) return true;
		}
			
		return false;
	}
	
	public function actionDelete() {
		if(isset($_POST["ids"])) {
			foreach (json_decode($_POST["ids"]) as $testId) {
				$test = AutomatedTest::findOne($testId);
				if(!$test->delete()) {
					return false;
				}
			}
			
			return true;
		}
		
		return false;
	}
}
