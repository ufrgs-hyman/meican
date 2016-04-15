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
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class CafeUserForm extends Model {
    
    public $login;
    public $passConfirm;
    public $password;

    /**
     */
    public function rules()    {
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
