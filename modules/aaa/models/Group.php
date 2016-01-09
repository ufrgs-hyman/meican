<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\aaa\models;

use Yii;

/**
 * This is the model class for table "{{%group}}".
 *
 * @property integer $id
 * @property string $role_name
 * @property string $name
 * @property string $type
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Group extends \yii\db\ActiveRecord
{
    const GUEST_GROUP_ROLE = "g1";
    
    const TYPE_DOMAIN = "DOMAIN";
    const TYPE_SYSTEM = "SYSTEM";
    
    public $_role;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['name', 'type'], 'string', 'max' => 50],
            [['role_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t("aaa", 'Name'),
            'type' => Yii::t("aaa", 'Type'),
        ];
    }
    
    public function delete() {
        $roles = UserDomainRole::findByGroup($this)->all();
        foreach ($roles as $role) {
            $role->delete();
        }
        
        try {
            $connection=Yii::$app->db;
            $command=$connection->createCommand(
                    "DELETE FROM meican_auth_item WHERE name='".$this->role_name."'")->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function setPermissions($permissions) {
        if ($this->isNewRecord) {
            return false;
        }
        
        $auth = Yii::$app->authManager;
        
        $role = $auth->getRole($this->role_name);
        if ($role) {
            $auth->removeChildren($role);
        }
        
        foreach($permissions as $permission) {
            $action = $auth->getPermission($permission);
            if ($action === null) {
                $action = $auth->createPermission($permission);
                $auth->add($action);
            }
            $auth->addChild($role, $action);
        }
        
        return true;
    }
    
    public function getPermissions() {
        $auth = Yii::$app->authManager;
        
        $childsChecked = [];
        foreach($auth->getPermissionsByRole($this->role_name) as $permission) {
            $childsChecked[] = $permission->name;
        }
        
        return $childsChecked;
    }
    
    public function beforeSave($isNewRecord) {
        if ($isNewRecord) {
            $auth = Yii::$app->authManager;
            $this->_role = 1;

            while ($this->_role != null) {
                $this->role_name = Yii::$app->getSecurity()->generateRandomString();
                
                $this->_role = $auth->getRole($this->role_name);
            }
            
            $this->_role = $auth->createRole($this->role_name);
            $auth->add($this->_role);
        }
        
        return parent::beforeSave($isNewRecord);
    }
    
    public function afterSave($isNewRecord, $changedAttributes) {
        if ($isNewRecord) {
            $auth = Yii::$app->authManager;
            
            $this->_role->name = "g".$this->id;
            
            //Grupo eh atualizado em cascata pelo db
            $auth->update($this->role_name, $this->_role);
            
            //para continuar basta atualizar o objeto nao persistido
            $this->role_name = $this->_role->name;
        }
         
        return parent::afterSave($isNewRecord, $changedAttributes);
    }
    
    public function getType(){
        if(self::TYPE_DOMAIN == $this->type) return Yii::t("aaa", 'Domain');
        else return Yii::t("aaa", 'System');
    }
}
