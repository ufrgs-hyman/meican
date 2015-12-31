<?php

namespace meican\notification\controllers;

use Yii;
use meican\aaa\RbacController;
use meican\notification\models\Notification;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionAuth;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;

class NotificationController extends RbacController {
	
	const TYPE_AUTHORIZATION = 	"AUTHORIZATION";
	const TYPE_RESERVATION = 	"RESERVATION";
	const TYPE_TOPOLOGY = 		"TOPOLOGY";
	
	public $enableCsrfValidation = false;
	
	public function actionIndex(){
		$dataProvider = new ActiveDataProvider([
				'query' => Notification::find()->where(['user_id' => Yii::$app->user->getId()])->orderBy(['date' => SORT_DESC]),
				'sort' => false,
				'pagination' => false,
		]);
		
		return $this->render('/index', array(
				'data' => $dataProvider,
		));
	}
	
	public function actionGetNumberNotifications(){
		echo Notification::getNumberNotifications();
	}
	
	public function actionGetNumberAuthorizations(){
		echo Notification::getNumberAuthorizations();
	}
	
	public function actionGetNotifications(){
		if(isset($_POST['date'])) echo json_encode(Notification::getNotifications($_POST['date']));
		else echo json_encode(Notification::getNotifications(null));
	}
	
}