<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * User entity. A element of the entity represents a identity.
 *
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property string $authkey
 * @property string $email
 * @property string $name
 * @property string $language
 * @property string $date_format
 * @property string $time_format
 * @property string $time_zone
 *
 * @property UserSettings $usersettings
 *
 * @author Maurício Quatrin Guerreiro
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
            [['login', 'password', 'authkey','language','name','email',
            'date_format','time_format','time_zone'], 'required'],
            [['language'], 'string'],
            [['time_format'], 'string', 'max' => 10],
            [['date_format'], 'string', 'max' => 20],
            [['login'], 'string', 'max' => 30],
            [['time_zone'], 'string', 'max' => 40],
            [['email'], 'string', 'max' => 60],
            [['email'], 'email'],
            [['password'], 'string', 'max' => 200],
            [['authkey', 'name'], 'string', 'max' => 100],
            [['login'], 'unique'],
            [['email'], 'unique'],
            [['authkey'], 'unique'],
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
     * @return UserSettings objeto associado ao usuario.
     */
    public function getUserSettings()
    {
        return $this->hasOne(UserSettings::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(UserDomainRole::className(), ['user_id' => 'id']);
    }

    static function findOneByEmail($email) {
        return self::find()->where(['email'=> $email])->one();
    }

    public static function findByUsername($username) {
        return static::findOne(['login' => $username]);
    }
    
    public static function findIdentity($id) {
        return static::findOne($id);
    }
    
    /**
     * login by token disabled
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return null;
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
    
    public function setFromData($login, $password, $name, $email, 
        $groupRoleName, $domain = null) {
        $this->login = $login;
        $this->authkey = Yii::$app->getSecurity()->generateRandomString();
        $this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
        $this->_groupRoleName = $groupRoleName;
        $this->_domain = $domain;
        
        $this->language = 'en-US';
        $this->date_format = 'dd/mm/yyyy';
        $this->time_format = "HH:mm";
        $this->time_zone = 'UTC';
        $this->name = $name;
        $this->email = $email;
    }
    
    public function afterSave($isNewRecord, $changedAttributes) {
        if ($isNewRecord) {
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
