<?php

namespace meican\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property string $authkey
 *
 * @property Request[] $requests
 * @property UserSettings $usersettings
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
	public $_settings;
	public $_groupRoleName;
	public $_domain;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'password', 'authkey'], 'required'],
            [['login'], 'string', 'max' => 30],
            [['password'], 'string', 'max' => 200],
            [['authkey'], 'string', 'max' => 100],
            [['login'], 'unique'],
            [['authkey'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => Yii::t('aaa', 'User'),
            'password' => Yii::t('aaa', 'Password'),
            'authkey' => 'Authkey',
            'name' => Yii::t('aaa', 'Name'),
            'language' => Yii::t('aaa', 'Language'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequests()
    {
        return $this->hasMany(Request::className(), ['response_user_id' => 'id']);
    }

    /**
     * @return UserSettings objeto associado ao usuario.
     */
    public function getUserSettings()
    {
    	return $this->hasOne(UserSettings::className(), ['id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDomainRoles()
    {
    	return $this->hasMany(UserDomainRole::className(), ['user_id' => 'id']);
    }

    /**
     * findOne segue o padrão do yii2 e retorna nulo em caso de não encontrar nenhum item
     */
    static function findOneByEmail($email) {
        $sets = UserSettings::findByEmail($email)->one();
        if ($sets) return $sets->getUser()->one();
        return null;
    }
    
    /**
     * TODO revisar, nao segue o padrao yii2
     */
    public static function findByUsername($username) {
    	return static::findOne(['login' => $username]);
    }
    
    public static function findIdentity($id) {
    	return static::findOne($id);
    }
    
    public static function findIdentityByAccessToken($token, $type = null) {
    	return static::findOne(['authkey' => $token]);
    }
    
    public function getName() {
    	return $this->getUserSettings()->one()->name;
    }
    
    public function getEmail() {
    	return $this->getUserSettings()->one()->email;
    }
    
    public function getId() {
    	return $this->id;
    }
    
    public function getAuthKey() {
    	return $this->authkey;
    }
    
    public function validateAuthKey($authKey) {
    	return $this->authkey === $authKey;
    }
    
    public function isValidPassword($password) {
    	if(Yii::$app->getSecurity()->validatePassword($password, $this->password)) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    public function setFromUserForm($form) {
    	$this->login = $form->login;
    	$this->authkey = Yii::$app->getSecurity()->generateRandomString();
    	$this->password = Yii::$app->getSecurity()->generatePasswordHash($form->password);
    	
    	//Save user settings with default
    	$this->_settings = $this->getUserSettings()->one();
    	if (!$this->_settings) $this->_settings = new UserSettings;
    	$this->_settings->language = 'en-US';
    	$this->_settings->date_format = 'dd/mm/yyyy';
    	$this->_settings->name = $form->name;
    	$this->_settings->email = $form->email;
    	
    	if(!$this->_settings->validate()) return $this->_settings->getErrors();
    	return false;
    }

    public function setFromData($login, $password, $name, $email, 
        $groupRoleName, $domain = null) {
        $this->login = $login;
        $this->authkey = Yii::$app->getSecurity()->generateRandomString();
        $this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
        $this->_groupRoleName = $groupRoleName;
        $this->_domain = $domain;
        
        $this->_settings = $this->getUserSettings()->one();
        if (!$this->_settings) $this->_settings = new UserSettings;
        $this->_settings->language = 'en-US';
        $this->_settings->date_format = 'dd/mm/yyyy';
        $this->_settings->name = $name;
        $this->_settings->email = $email;
    }
    
    public function afterSave($isNewRecord, $changedAttributes) {
    	if ($isNewRecord) {
    		$this->_settings->id = $this->id;
    		$this->_settings->save();
    		
    		if(isset($this->_groupRoleName)){
    			$userDomainRole = new UserDomainRole;
	    		$userDomainRole->user_id = $this->id;
	    		$userDomainRole->domain = $this->_domain;
	    		$userDomainRole->_groupRoleName = $this->_groupRoleName;
	    		$userDomainRole->save();
    		}
    	}
    
    	return parent::afterSave($isNewRecord, $changedAttributes);
    }
}
