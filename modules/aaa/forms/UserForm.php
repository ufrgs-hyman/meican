<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\forms;

use yii\base\Model;
use Yii;

use meican\aaa\models\User;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class UserForm extends Model {

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = "update";
    const SCENARIO_UPDATE_ACCOUNT = 'update_account';
    
    public $login;
    public $isChangedPass;
    public $currentPass;
    public $newPass;
    public $newPassConfirm;
    public $email;
    public $name;
    public $language;
    public $dateFormat;
    public $timeFormat;
    public $timeZone;

    /**
     */
    public function rules() {

        $rules = [
            [['name', 'language', 'email', 'dateFormat', 'timeFormat', 'timeZone'], 'required'],
            ['newPassConfirm', 'compare', 'compareAttribute'=> 'newPass'],
            [['isChangedPass','currentPass','newPass', 'newPassConfirm'], 'validatePass'],
            [['login'], 'safe'],
        ];

        if($this->scenario == self::SCENARIO_CREATE){
            $rules[] = [['newPass', 'newPassConfirm'], 'required'];
        }elseif($this->scenario == self::SCENARIO_UPDATE_ACCOUNT){
            $rules[] = [
                ['currentPass', 'newPass', 'newPassConfirm'], 'required',
                'when' => function($model){
                    return false;
                },
                'whenClient' => "function (attribute, value) {
                    return $('.icheckbox_minimal-blue').hasClass('checked');
                }"
            ];
        }elseif ($this->scenario == self::SCENARIO_UPDATE) {
            $rules[] = [
                ['newPass', 'newPassConfirm'], 'required',
                'when' => function($model){
                    return false;
                },
                'whenClient' => "function (attribute, value) {
                    return $('.icheckbox_minimal-blue').hasClass('checked');
                }"
            ];
        }
        return $rules;
    }
    
    public function attributeLabels() {
        return [
            'login'=>Yii::t('aaa', 'Login'),
            "password"=>Yii::t('aaa', 'Password'),
            "isChangedPass" => (
                $this->scenario == self::SCENARIO_UPDATE ? Yii::t('aaa', 'I want to change the password') : Yii::t('aaa', 'I want to change my password')
            ),
            "newPass" => (
                $this->scenario == self::SCENARIO_CREATE ? Yii::t('aaa', 'Password') : Yii::t('aaa', 'New password')
            ),
            "newPassConfirm"=> (
                $this->scenario == self::SCENARIO_CREATE ? Yii::t('aaa', 'Confirm password') : Yii::t('aaa', "Confirm new password")
            ),
            'currentPass' => Yii::t('aaa', 'Current password'),
            'language' => Yii::t('aaa', 'Language'),
            'name' => Yii::t('aaa', 'Name'),
            'email' => Yii::t('aaa', 'Email'),
            'dateFormat' => Yii::t('aaa', 'Date Format'),
            'timeZone' => Yii::t('aaa', 'Time Zone'),
            'timeFormat' => Yii::t("aaa", "Time Format")
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['login', 'name', 'language', 'email', 'dateFormat',
         'timeFormat','timeZone','newPass','newPassConfirm'];
        $scenarios[self::SCENARIO_UPDATE] = ['login', 'name', 'language', 'email', 'dateFormat', 
        'timeFormat','timeZone','newPass','newPassConfirm','isChangedPass'];
        $scenarios[self::SCENARIO_UPDATE_ACCOUNT] = ['name', 'language', 'email', 'dateFormat', 'timeFormat', 
        'timeZone','newPass','newPassConfirm','currentPass','isChangedPass'];
        return $scenarios;
    }
    
    public function setFromRecord($record) {
        $this->login = $record->login;
        $this->name = $record->name;
        $this->email = $record->email;
        $this->language = $record->language;
        $this->dateFormat = $record->date_format;
        $this->timeZone = $record->time_zone;
    }

    public function validatePass($attr, $params) {
        if ($this->isChangedPass) {

            if (($this->currentPass == '' && $this->scenario == self::SCENARIO_UPDATE_ACCOUNT) ||
                $this->newPass == '' || $this->newPassConfirm == '') {
                $this->addError('currentPass', 'All password fields are required');

            } else {

                if($this->scenario == self::SCENARIO_UPDATE){
                    return true;
                }

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

    public function createUser($user) {
        $user->login = $this->login;
        $user->authkey = Yii::$app->getSecurity()->generateRandomString();
        $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->newPass);
        
        $user->language = $this->language;
        $user->date_format = $this->dateFormat;
        $user->time_zone = $this->timeZone;
        $user->time_format = $this->timeFormat;
        $user->name = $this->name;
        $user->email = $this->email;

        return $user->save();
    }
    
    public function updateUser($user) {
        if ($this->isChangedPass) {
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($this->newPass);
        }

        $user->name = $this->name;
        $user->email = $this->email;
        $user->language = $this->language;
        $user->date_format = $this->dateFormat;
        $user->time_format = $this->timeFormat;
        $user->time_zone = $this->timeZone;
        
        return $user->save();
    }
    
    public function clearPass() {
        $this->isChangedPass = false;
        $this->newPass = '';
        $this->newPassConfirm = '';
        $this->currentPass = '';
    }
}
