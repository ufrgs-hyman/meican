<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\notify\controllers;

use Yii;
use meican\aaa\RbacController;
use meican\notify\models\Notification;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionAuth;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;

/**
 * @author Diego Pittol
 * @author Maurício Quatrin Guerreiro
 */
class ServiceController extends RbacController {
	
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
	
	public function actionGetSize(){
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