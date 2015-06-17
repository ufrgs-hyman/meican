<?php

namespace app\modules\aaa\models;

use yii\base\Model;
use Yii;
use app\models\User;

/**
 */
class UserForm extends Model {
	
	public $id;
	public $login;
	public $password;
	public $email;
	public $name;
	public $domain;
	public $group;

	/**
	 */
	public function rules()	{
		return [
			[['login', 'name', 'email'], 'required'],
			[['group', 'password'], 'required', 'on'=> 'create'],
			[['group', 'domain', 'password'], 'safe'],
		];
	}
	
	public function attributeLabels() {
		return [
			'login'=>Yii::t('aaa', 'User'),
			"password"=>Yii::t('aaa', 'Password'),
			'name' => Yii::t('aaa', 'Name'),
			'email' => Yii::t('aaa', 'Email'),
			'group' => Yii::t('aaa', 'Group'),
			'domain' => Yii::t('aaa', 'Domain'),
		];
	}
	
	public function setFromRecord($record) {
		$this->id = $record->id;
		$this->login = $record->login;
		$this->name = $record->name;
		$this->email = $record->email;
	}
	
	public function updateUser($user) {
		$user->login = $this->login;
		if ($this->password != "") {
			$user->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
		}
		
		return $user->save();
	}
	
	public function updateSettings($settings) {
		$settings->name = $this->name;
		$settings->email = $this->email;
		
		return $settings->save();
	}
}
