<?php

namespace meican\modules\init\models;

use yii\base\Model;
use Yii;
use meican\models\User;
use meican\models\UserSettings;

class ForgotPasswordForm extends Model {

	public $login;
	public $email;
	public $sendMail;

	public function rules()	{
		return [
				[['login', 'email'], 'safe'],
		];
	}

	public function attributeLabels() {
		return [
				'login'=>Yii::t('init', 'User'),
				'email'=>Yii::t('init', 'Email'),
		];
	}

	public function checkCamps() {
		if($this->login != "" || $this->email != ""){
			if($this->login != "" && $this->email != ""){
				$user = User::findByUsername($this->login);
				if(!isset($user)){
					$this->addError($this->login, Yii::t('init', 'The user you entered is incorrect'));
					return false;
				}

				$userSettings = UserSettings::findOne(['email' => $this->email]);
				if($this->email == UserSettings::findOne(['id' => $user->id])->email) return true;
				else $this->addError($this->login, Yii::t('init', 'The email you entered is incorrect'));
			}
			else if($this->login != ""){
				$user = User::findByUsername($this->login);
				if(isset($user)) return true;
				else $this->addError($this->login, Yii::t('init', 'The user you entered is incorrect'));
			}
			else {
				$userSettings = UserSettings::findOne(['email' => $this->email]);
				if(isset($userSettings)) return true;
				else $this->addError($this->login, Yii::t('init', 'The email you entered is incorrect'));
			}
		}
		else $this->addError($this->login, Yii::t('init', 'Please insert your user or email'));
		return false;
	}

	public function sendEmail(){
		$user;
		if($this->login != "") $user = User::findByUsername($this->login);
		else $user = User::findOne(['id' => UserSettings::findOne(['email' => $this->email])->id]);

		$newPassword = $this->generateRandomString();

		$body = "Hello. Your new password for Meican is: \n\n";
		$body .= $newPassword;

		$body .= "\n\nThis is an automated message, please do not respond.";

		Yii::trace($body);

		$mail = Yii::$app->mailer->compose()
		->setFrom('meican@inf.ufrgs.br')
		->setTo(UserSettings::findOne(['id' => $user->id])->email)
		->setSubject('Meican new Password')
		->setTextBody($body);

		if ($mail->send()){
			Yii::trace("Email send to: ".UserSettings::findOne(['id' => $user->id])->email);
			$user->password = Yii::$app->getSecurity()->generatePasswordHash($newPassword);
			$user->save();
			return true;
		}
		else{
			$this->addError($this->login, Yii::t('init', 'An error occured, please try again'));
			return false;
		}
	}

	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
