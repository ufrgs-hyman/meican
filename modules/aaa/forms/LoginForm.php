<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\aaa\forms;

use yii\base\Model;
use Yii;

use meican\aaa\models\User;

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'validate' action of LoginController.
 * 
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class LoginForm extends Model {
    
    public $login;
    public $password;
    private $_user = false;

    public function rules()    {
        return [
            [['login', 'password'], 'required'],
            [['password'], 'authenticate'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'login'=>Yii::t('aaa', 'Login'),
            "password"=>Yii::t('aaa', 'Password'),
        ];
    }
    
    public function authenticate($attribute,$params) {
        if(!$this->hasErrors())    {
            $user = $this->getUser();
            if (!$user || !$user->isValidPassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login()    {
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
