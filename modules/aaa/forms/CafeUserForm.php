<?php

namespace meican\aaa\forms;

use yii\base\Model;
use Yii;

use meican\aaa\models\User;

/**
 */
class CafeUserForm extends Model {
	
	public $login;
	public $passConfirm;
	public $password;

	/**
	 */
	public function rules()	{
		return [
			[['login', 'password','passConfirm'], 'required'],
			['password', 'compare', 'compareAttribute'=> 'passConfirm'],
		];
	}
	
	public function attributeLabels() {
		return [
			'login'=>Yii::t('init', 'User'),
			"password"=>Yii::t('init', 'Password'),
			"passConfirm"=> Yii::t('init', "Confirm password"),
		];
	}
}
