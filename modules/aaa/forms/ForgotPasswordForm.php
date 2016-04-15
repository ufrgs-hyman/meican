<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\aaa\forms;

use yii\base\Model;
use Yii;

use meican\aaa\models\User;
use meican\aaa\models\UserSettings;

class ForgotPasswordForm extends Model {

    public $login;
    public $email;
    public $sendMail;

    public function rules()    {
        return [
        		[['email'], 'email'],
        		['login', 'validateFields', 'skipOnEmpty' => false, 'skipOnError' => false],
        ];
    }

    public function attributeLabels() {
        return [
                'login'=>Yii::t('home', 'User'),
                'email'=>Yii::t('home', 'Email'),
        ];
    }
    
    public function validateFields($attribute, $params) {
        if($this->login != "" || $this->email != ""){
            if($this->login != "" && $this->email != ""){
                $user = User::findByUsername($this->login);
                if(!isset($user)){
                    $this->addError('login', Yii::t('home', 'The user you entered is incorrect'));
                    return false;
                }

                if($this->email == $user->email) return true;
                else $this->addError('email', Yii::t('home', 'The email you entered is incorrect'));
            }
            else if($this->login != ""){
                $user = User::findByUsername($this->login);
                if(isset($user)) return true;
                else $this->addError('login', Yii::t('home', 'The user you entered is incorrect'));
            }
            else {
                $user = User::findOne(['email' => $this->email]);
                if(isset($user)) return true;
                else $this->addError('email', Yii::t('home', 'The email you entered is incorrect'));
            }
        }
        else {
        	$this->addError('login', Yii::t('home', 'Please insert your user or email'));
        	$this->addError('email', Yii::t('home', 'Please insert your user or email'));
        }
        return false;
    }

    public function sendEmail(){
        $user;
        if($this->login != "") $user = User::findByUsername($this->login);
        else $user = User::findOne(['email' => $this->email]);

        $newPassword = $this->generateRandomString();

        $body = "Hello. Your new password for Meican is: \n\n";
        $body .= $newPassword;

        $body .= "\n\nThis is an automated message, please do not respond.";

        Yii::trace($body);

        $mail = Yii::$app->mailer->compose()
        ->setFrom('meican@inf.ufrgs.br')
        ->setTo($user->email)
        ->setSubject('Meican new Password')
        ->setTextBody($body);

        if ($mail->send()){
            Yii::trace("Email send to: ".$user->email);
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($newPassword);
            $user->save();
            return true;
        }
        else{
            $this->addError($this->login, Yii::t('home', 'An error occured, please try again'));
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
