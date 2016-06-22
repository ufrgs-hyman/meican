<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\home\controllers;

use Yii;
use yii\helpers\Url;

use meican\base\BaseController;
use meican\base\utils\DateUtils;
use meican\aaa\models\User;
use meican\home\forms\FeedbackForm;

/**
 * @author Maurício Quatrin Guerreiro
 */
class SupportController extends BaseController {
	
	public function actionHelp() {
		return $this->render('help');
	}
	
	public function actionAbout() {
		return $this->render('about');
	}
	
	public function actionSendFeedback(){
		$user = Yii::$app->user->getIdentity();
        $form = new FeedbackForm;
        $form->load($_POST);
		
		$body = "User: " . $user->name. "\n";
		$body .= "E-mail: " . $user->email . "\n\n";

		$body .= "Title: " . $form->subject . "\n";
		$body .= "Message: " . $form->message . "\n";
	
		Yii::trace($body);
	
		$mail = Yii::$app->mailer->compose()
			->setFrom(Yii::$app->params['mailer.destination'])
			->setTo(Yii::$app->params['mailer.destination'])
			->setSubject('Feedback Meican')
			->setTextBody($body);
		
		if ($mail->send())
			return true;
		else
			return false;
	}
}

?>