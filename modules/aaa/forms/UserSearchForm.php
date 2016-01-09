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
 */
class UserSearchForm extends Model {
    
    public $id;
    public $login;
    public $name;
    public $numRoles;

    /**
     */
    public function rules()    {
        return [
            [['login', 'name', 'id', 'numRoles'], 'required'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'login'=>Yii::t('aaa', 'User'),
            "numRoles"=>Yii::t('aaa', 'Roles in Domain'),
            'name' => Yii::t('aaa', 'Name'),
        ];
    }
    
    public function setData($user, $numRoles) {
        $this->id = $user->id;
        $this->login = $user->login;
        $this->name = $user->name;
        $this->numRoles = $numRoles;
    }
}
