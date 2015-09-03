<?php

namespace app\modules\circuits\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use Yii;
use app\models\AutomatedTest;
use app\models\Domain;
use yii\data\ActiveDataProvider;
use app\modules\circuits\models\AutomatedTestForm;

class AutomatedTestController extends RbacController {
	
	public $enableCsrfValidation = false;

	public function actionIndex() {
		//self::canRedir('topology/create');
		
		foreach (AutomatedTest::find()->all() as $test) {
			$test->deleteIfInvalid();
		}
		
		$data = new ActiveDataProvider([
    			'query' => AutomatedTest::find()->where(['<>', 'status', AutomatedTest::STATUS_DELETED]),
    			'sort' => false,
    			]);
			
		return $this->render('/tests/view', array(
				'data' => $data,
				'start_time' => "14:00",
				'domains' => json_encode(Domain::find()->orderBy(['name'=> "SORT ASC"])->asArray()->select(['id','name'])->all()),
		));
	}
	
	public function actionCreate() {
		$form = new AutomatedTestForm;
		if ($form->load($_POST)) {
			if ($form->save()) {
				return $form->reservation->id;
			}
		}

		$this->checkRequesterUrl();
		 
		return null;
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
			$test = AutomatedTest::findOne($id);
			$test->frequency_type = $form->freq_type;
			$test->crontab_frequency = $form->freq_value;
			$test->status = AutomatedTest::STATUS_PROCESSING;
			$test->crontab_changed = 1;
			$test->save();
		}
			
		return null;
	}
	
	public function actionDelete() {
		//if(!self::can('topology/delete')) return false;
		
		if(isset($_POST["ids"])) {
			foreach (json_decode($_POST["ids"]) as $testId) {
				$test = AutomatedTest::findOne($testId);
				$test->status = AutomatedTest::STATUS_DELETED;
				$test->crontab_changed = true;
				if(!$test->save()) {
					return false;
				}
			}
			
			return true;
		}
		
		return false;
	}
}
