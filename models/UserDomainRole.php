<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_domain}}".
 *
 * @property integer $user_id
 * @property integer $domain_id
 *
 * @property Domain $domain
 * @property User $user
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
            [['user_id', 'domain_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User',
            'domain_id' => Yii::t("aaa", "Domain"),
            '_groupRoleName'	=> Yii::t("aaa", "Group"),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id'])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->one();
    }
    
    public function getValidDomains($isUpdate = false) {
    	$domains = self::getDomains();
    	$udrs = self::findAll(['user_id'=>$this->user_id]);
    	foreach ($domains as $key => $dom) {
    		foreach ($udrs as $udr) {
    			if ($dom['id'] == $udr->domain_id) {
    				unset($domains[$key]);
    			}
    		}
    	}
    	if ($isUpdate) {
    		$domName = Yii::t("aaa" , "Any");
    		if ($this->getDomain()) {
    			$domName = $this->getDomain()->name;
    		}
    		return array_merge([['id'=>$this->domain_id,'name'=>$domName]], $domains);
    	}
    	return $domains;
    }
    
    static function getDomains() {
    	return array_merge([['id'=>null,'name'=>Yii::t("aaa" , "Any")]], Domain::find()->orderBy(['name'=> "SORT ASC"])->asArray()->all());
    }
    
    static function getGroups() {
    	return Group::find()->asArray()->all();
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
}
