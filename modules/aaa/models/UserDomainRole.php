<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\models;

use Yii;

use meican\topology\models\Domain;

/**
 * Represents a Role of User instance. Each role
 * contains some permissions.
 *
 * @property integer $user_id
 * @property string $domain
 *
 * @property Domain $domain
 * @property User $user
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class UserDomainRole extends \yii\db\ActiveRecord
{
    public $_groupRoleName;
    public $_oldGroupRoleName;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_domain}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', '_groupRoleName'], 'required'],
            [['user_id'], 'integer'],
            [['domain'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User',
            'domain' => Yii::t("aaa", "Domain"),
            '_groupRoleName'    => Yii::t("aaa", "Group"),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['name' => 'domain'])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->one();
    }
    
    public function getValidDomains($allowed_domains, $isUpdate = false) {
        
        $domains = self::getDomains($allowed_domains);
        $udrs = self::findAll(['user_id'=>$this->user_id]);
        foreach ($domains as $key => $dom) {
            foreach ($udrs as $udr) {
                if ($dom['id'] == $udr->domain) {
                    unset($domains[$key]);
                }
            }
        }
        if ($isUpdate) {
            if ($this->getDomain()) {
                $domName = $this->getDomain()->name;
            }
            if(isset($domName)) return array_merge([['id'=>$this->domain,'name'=>$domName]], $domains);
        }
        return $domains;
    }
    
    static function getDomains($allowed_domains) {
        $domains_name = [];
        foreach($allowed_domains as $domain) $domains_name[] = $domain->name;
        return Domain::find()->where(['in', 'name', $domains_name])->orderBy(['name'=> "SORT ASC"])->asArray()->all();
    }
    
    static function getGroups() {
        return Group::find()->orderBy(['name' => SORT_ASC])->asArray()->all();
    }
    
    static function getDomainGroups() {
        return Group::find()->where(['type' => Group::TYPE_DOMAIN])->orderBy(['name' => SORT_ASC])->asArray()->all();
    }
    
    static function getDomainGroupsNoArray() {
        return Group::find()->where(['type' => Group::TYPE_DOMAIN])->orderBy(['name' => SORT_ASC])->all();
    }
    
    static function getSystemGroups() {
        return Group::find()->where(['type' => Group::TYPE_SYSTEM])->orderBy(['name' => SORT_ASC])->asArray()->all();
    }
    
    static function getSystemGroupsNoArray() {
        return Group::find()->where(['type' => Group::TYPE_SYSTEM])->orderBy(['name' => SORT_ASC])->all();
    }
    
    static function getGroupsNoArray() {
        return Group::find()->all();
    }
    
    static function findByGroup($group) {
        return static::find()
            ->join('LEFT JOIN','meican_auth_assignment','meican_auth_assignment.user_id = id')
            ->where(['meican_auth_assignment.item_name' => $group->role_name]);
    }
    
    public function getGroup() {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($this->id);
        foreach ($roles as $role) {
            $group = Group::findOne(['role_name'=>$role->name]);
            $this->_groupRoleName = $group->role_name;
            $this->_oldGroupRoleName = $this->_groupRoleName;
            return $group;
        }
    }
    
    public function afterSave($isNewRecord, $changedAttributes) {
        $auth = Yii::$app->authManager;
         
        $groupRole = $auth->getRole($this->_groupRoleName);
        
        if ($isNewRecord) {
            $auth->assign($groupRole, $this->id);
            
        } else {
            $oldRole = $auth->getRole($this->_oldGroupRoleName);
            $auth->revoke($oldRole, $this->id);
            $auth->assign($groupRole, $this->id);
        }
    
        return parent::afterSave($isNewRecord, $changedAttributes);
    }
    
    static function getDomainGroupsByDomainNoArray($domain) {
    	return Group::find()->where(['type' => Group::TYPE_DOMAIN, 'domain' => $domain])->orderBy(['name' => SORT_ASC])->all();
    }
    
    static function getGlobalDomainGroupsNoArray() {
    	return Group::find()->where(['type' => Group::TYPE_DOMAIN, 'domain' => null])->orderBy(['name' => SORT_ASC])->all();
    }

    public function getUserDomain(){
        $userDomain = $this->domain;
        return $userDomain;
    }
}
