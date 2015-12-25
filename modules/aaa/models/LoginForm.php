<?php

namespace meican\modules\aaa\models;

use yii\base\Model;
use Yii;
use meican\models\User;

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'validate' action of LoginController.
 */
class LoginForm extends Model {
	
	public $login;
	public $password;
	private $_user = false;

	public function rules()	{
		return [
			[['login', 'password'], 'required'],
			[['password'], 'authenticate'],
		];
	}
	
	public function attributeLabels() {
		return [
			'login'=>Yii::t('init', 'Login'),
			"password"=>Yii::t('init', 'Password'),
		];
	}
	
	public function authenticate($attribute,$params) {
		if(!$this->hasErrors())	{
			$user = $this->getUser();
			if (!$user || !$user->isValidPassword($this->password)) {
				$this->addError($attribute, 'Incorrect username or password.');
			}
		}
	}

	public function login()	{
		if ($this->validate()) {
			return $this->createSession($this->getUser());
		} else {
			return false;
		}
	}
	
	public function createSession($user) {
		$duration = 3600; // one hour
		return Yii::$app->user->login($user, $duration);
	}
	
	public function getUser() {
		if ($this->_user === false) {
			$this->_user = User::findByUsername($this->login);
		}
		return $this->_user;
	}
}
