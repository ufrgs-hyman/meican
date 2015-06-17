<?php

namespace app\modules\aaa\models;

use yii\base\Model;
use Yii;
use app\models\User;

/**
 */
class AccountForm extends Model {
	
	public $login;
	public $isChangedPass;
	public $currentPass;
	public $newPass;
	public $newPassConfirm;
	public $email;
	public $name;
	public $language;
	public $dateFormat;

	/**
	 */
	public function rules()	{
		return [
			[['name', 'language', 'email'], 'required'],
			['newPass', 'compare', 'compareAttribute'=> 'newPassConfirm'],
			[['isChangedPass','currentPass','newPass', 'newPassConfirm'], 'validatePass'],
			[['login'], 'safe']
		];
	}
	
	public function attributeLabels() {
		return [
			'login'=>Yii::t('aaa', 'User'),
			"password"=>Yii::t('aaa', 'Password'),
			"isChangedPass" => Yii::t('aaa', 'I want change my password'),
			"newPass" => Yii::t('aaa', 'New password'),
			"newPassConfirm"=> Yii::t('aaa', "Confirm new password"),
			'currentPass' => Yii::t('aaa', 'Current password'),
			'language' => Yii::t('aaa', 'Language'),
			'name' => Yii::t('aaa', 'Name'),
			'email' => Yii::t('aaa', 'Email'),
			'dateFormat' => Yii::t('aaa', 'Date Format'),
		];
	}
	
	public function setFromRecord($record) {
		$this->login = $record->login;
		$sets = $record->getUserSettings()->one();
		$this->name = $sets->name;
		$this->email = $sets->email;
		$this->language = $sets->language;
		$this->dateFormat = $sets->date_format;
	}
	
	public function validatePass($attr, $params) {
		if ($this->isChangedPass) {
			
			if ($this->currentPass == '' || $this->newPass == '' || $this->newPassConfirm == '') {
				$this->addError('', 'All password fields are required');
				
			} else {
				$user = User::findOne(Yii::$app->user->id);
				
				if ($user->isValidPassword($this->currentPass)) {
					return true;
				} else {
					$this->addError('currentPass', Yii::t('aaa', 'Current password does not match'));
				}
			}
		}
		return false;
	}
	
	public function updateUser($user) {
		if ($this->isChangedPass) {
			$user->password = Yii::$app->getSecurity()->generatePasswordHash($this->newPass);
		}
		
		return $user->save();
	}
	
	public function updateSettings($settings) {
		$settings->language = $this->language;
		$cookies = Yii::$app->response->cookies;
		$cookies->add(new \yii\web\Cookie([
		    'name' => 'language',
		    'value' => $this->language,
		]));
		
		//$settings->date_format = $this->dateFormat;
		$settings->name = $this->name;
		$settings->email = $this->email;
		
		return $settings->save();
	}
	
	public function clearPass() {
		$this->isChangedPass = false;
		$this->newPass = '';
		$this->newPassConfirm = '';
		$this->currentPass = '';
	}
}
