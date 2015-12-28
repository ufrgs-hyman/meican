<?php

namespace meican\home\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;

use meican\base\components\DateUtils;
use meican\aaa\models\User;

class SupportController extends Controller {
	
	public $enableCsrfValidation = false;
	
	public function actionHelp() {
		return $this->render('help');
	}
	
	public function actionAbout() {
		return $this->render('about');
	}
	
	public function actionWaitGetServerTime() {
		self::asyncActionBegin();
		sleep(60);
		return json_encode(DateUtils::serverTime());
	}
	
	public function actionGetServerTime() {
		return json_encode(DateUtils::serverTime());
	}
	
	public function actionSendEmail(){
		$user = User::findOne(['id' => Yii::$app->user->getId()]);
		
		$body = "User: " . $user->getName(). "\n";
		$body .= $user->getEmail() ? "E-mail: " . $user->getEmail() . "\n\n" : "\n";

		$body .= "Type: " . $_POST['topic_style'] . "\n\n";
	
		$body .= "Title: " . $_POST['topic_subject'] . "\n";
		$body .= "Message: " . $_POST['topic_additional_detail'] . "\n";
	
		$body .= "\n";
		$body .= "Makes me feel: " . $_POST['topic_emotitag_feeling'] . "\n";
		
		Yii::trace($body);
	
		$mail = Yii::$app->mailer->compose()
			->setFrom(Yii::$app->params['mailer.destination'])
			->setTo(Yii::$app->params['mailer.destination'])
			->setSubject('Feedback Meican')
			->setTextBody($body);
		
		if ($mail->send())
			echo Yii::t("init", 'Feedback sent. Thank you!');
		else
			echo Yii::t("init", 'Error sending feedback. Try again later.');

	}
}

?>