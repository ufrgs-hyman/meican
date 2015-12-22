<?php

namespace app\modules\init\models;

use yii\base\Model;
use Yii;
use app\models\User;

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
			'login'=>Yii::t('init', 'Username'),
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
		$sets = $user->getUserSettings()->one();
		$result = Yii::$app->user->login($user, $duration);
		if ($result) {
			Yii::$app->session["user.login"] = $user->login;
			Yii::$app->session["user.name"] = $sets->name;
			Yii::$app->session["language"] = $sets->language;
			Yii::$app->session["date.format"] = $sets->date_format;
			Yii::$app->session["time.zone"] = $sets->time_zone;
			Yii::$app->session["topo.viewer"] = $sets->topo_viewer;
		}
		return $result;
	}
	
	public function getUser() {
		if ($this->_user === false) {
			$this->_user = User::findByUsername($this->login);
		}
		return $this->_user;
	}
}
