<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\aaa\controllers;

use yii\data\ActiveDataProvider;
use Yii;

use meican\aaa\forms\UserForm;
use meican\aaa\models\User;
use meican\aaa\models\UserSettings;
use meican\aaa\models\UserDomainRole;
use meican\topology\models\Domain;
use meican\aaa\models\Group;
use meican\aaa\RbacController;
use meican\aaa\models\AaaNotification;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class RoleController extends RbacController {
    
    public function actionCreateRoleDomain($id) {
        if(!self::can("role/create") && !self::can("user/create")){
            if(!self::can("user/read") && !self::can("role/read")) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to create domain roles'));
                return $this->redirect(array('/aaa/user/view', 'id'=>$id));
            }
        }

        $udr = new UserDomainRole;
        $udr->user_id = $id;

        if(isset($_POST["UserDomainRole"])) {
            
            $form = $_POST["UserDomainRole"];
            
            $udr->_groupRoleName = $form["_groupRoleName"];
            $roleDomain = $form['domain'];
            if($roleDomain == "") $udr->domain = null;
            else $udr->domain = $roleDomain;
            
            $alreadyHas = false;
            $roles = UserDomainRole::find()->where(['domain' => $udr->domain, 'user_id' => $udr->user_id])->all();
            foreach($roles as $role){
                if($role->getGroup()->role_name == $udr->_groupRoleName){
                    $alreadyHas = true;
                    break;
                }
            }
            if($alreadyHas){
                Yii::$app->getSession()->setFlash("warning", Yii::t("aaa", 'The user already has this profile'));
                return $this->redirect(array('/aaa/user/view', 'id'=>$id));
            }
            else {
                if($udr->save()) {
                	AaaNotification::createRole($udr);
                    
                    Yii::$app->getSession()->setFlash("success", Yii::t("aaa", 'Role created successfully'));

                    return $this->redirect(array('/aaa/user/view', 'id'=>$id));
        
                } else {
                    foreach($udr->getErrors() as $attribute => $error) {
                        Yii::$app->getSession()->setFlash("error", $error[0]);
                    }
                }
            }
        }
        
        if(self::can("user/create")){
        	$anyDomain = [null=>Yii::t("aaa" , "any")];
        	$domains = Domain::find()->orderBy(['name' => SORT_ASC])->asArray()->all();
        }
        else{
        	$domains = self::whichDomainsCan("role/create");
        	$anyDomain = [];
        }
        
        $groups = [];
        foreach (UserDomainRole::getGlobalDomainGroupsNoArray() as $group):
    		$groups[$group->role_name] = $group->name;
    	endforeach;
    
        return $this->renderPartial('_add-role-domain',array(
                'udr' => $udr,
                'domains' => $domains,
        		'groups' => ["", ""],
                'anyDomain' => $anyDomain,
        ));
    }
    
    public function actionCreateRoleSystem($id) {
    	if(!self::can("user/create")){
    		if(!self::can("user/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to create system roles'));
    			return $this->redirect(array('/aaa/user/view', 'id'=>$id));
    		}
    	}
    
    	$udr = new UserDomainRole;
    	$udr->user_id = $id;
    
    	if(isset($_POST["UserDomainRole"])) {
    
    		$form = $_POST["UserDomainRole"];
    		
    		$udr->_groupRoleName = $form["_groupRoleName"];
    		$roleDomain = null;
    
    		$alreadyHas = false;
    		$roles = UserDomainRole::find()->where(['domain' => $udr->domain, 'user_id' => $udr->user_id])->all();
    		foreach($roles as $role){
    			if($role->getGroup()->role_name == $udr->_groupRoleName){
    				$alreadyHas = true;
    				break;
    			}
    		}
    		if($alreadyHas){
    			Yii::$app->getSession()->setFlash("warning", Yii::t("aaa", 'The user already has this profile'));
    			return $this->redirect(array('/aaa/user/view', 'id'=>$id));
    		}
    		else {
    			if($udr->save()) {
    				AaaNotification::createRole($udr);
    				
    				Yii::$app->getSession()->setFlash("success", Yii::t("aaa", 'Role created successfully'));
    
    				return $this->redirect(array('/aaa/user/view', 'id'=>$id));
    
    			} else {
    				foreach($udr->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->setFlash("error", $error[0]);
    				}
    			}
    		}
    	}   
    
	    $groups = [];
	    foreach($udr->getSystemGroupsNoArray() as $group) $groups[$group->role_name] = $group->name;

    	return $this->renderPartial('_add-role-system',array(
    			'udr' => $udr,
    			'groups' => $groups,
    	));
    }

    public function actionUpdateRoleDomain($id) {
        $udr = UserDomainRole::findOne($id);

        if(!$udr){
            if(!self::can("user/read") && !self::can("role/read", $udr->domain)) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Role not found'));
                return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
            }
        }
        if(!self::can("role/update", $udr->domain) && !self::can("user/update")){
            if(!self::can("user/read") && !self::can("role/read")) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to update roles'));
                return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
            }
        }

        $udr->getGroup();

        $group = $udr->getGroup();
        
        if(isset($_POST["UserDomainRole"])) {
            $form = $_POST["UserDomainRole"];
            
            $udr->_groupRoleName = $form["_groupRoleName"];
            $roleDomain = $form['domain'];
            if($roleDomain == "") $udr->domain = null;
            else $udr->domain = $roleDomain;
            
            $alreadyHas = false;
            $roles = UserDomainRole::find()->where(['domain' => $udr->domain, 'user_id' => $udr->user_id])->all();
            foreach($roles as $role){
                if($role->getGroup()->role_name == $udr->_groupRoleName){
                    $alreadyHas = true;
                    break;
                }
            }
            if($alreadyHas){
                Yii::$app->getSession()->setFlash("warning", Yii::t("aaa", 'The user already has this profile'));
                return $this->redirect(array('/aaa/user/view', 'id'=>$udr->user_id));
            }
            else {
                if($udr->save()) {
                	AaaNotification::createRole($udr);
                    
                	AaaNotification::deleteRole($udr, $group);
                    
                    Yii::$app->getSession()->setFlash("success", Yii::t("aaa", 'Role updated successfully'));
                    
                    return $this->redirect(array('/aaa/user/view', 'id'=>$udr->user_id));
                     
                } else {
                    foreach($udr->getErrors() as $attribute => $error) {
                        Yii::$app->getSession()->setFlash("error", $error[0]);
                    }
                }
            }
        }
        
        if(self::can("user/create")){
        	$anyDomain = [null=>Yii::t("aaa" , "any")];
        	$domains = Domain::find()->orderBy(['name' => SORT_ASC])->asArray()->all();
        }
    	else{
        	$domains = self::whichDomainsCan("role/create");
        	$anyDomain = [];
        }
        
        $groups = [];
        if($udr->domain != null){
	        foreach (UserDomainRole::getDomainGroupsByDomainNoArray($udr->domain) as $group):
	        	$groups[$group->role_name] = $group->name." (".$udr->domain.")";
	        endforeach;
        }
         
    	foreach (UserDomainRole::getGlobalDomainGroupsNoArray() as $group):
    		$groups[$group->role_name] = $group->name;
    	endforeach;
    
        return $this->renderPartial('_edit-role-domain',array(
                'udr' => $udr,
                'groups' => $groups,
                'domains' => $domains,
                'anyDomain' => $anyDomain,
        ));
    }
    
    public function actionUpdateRoleSystem($id) {
    	$udr = UserDomainRole::findOne($id);
    
    	if(!$udr){
    		if(!self::can("user/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Role not found'));
    			return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
    		}
    	}
    	if(!self::can("user/update")){
    		if(!self::can("role/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to update roles'));
    			return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
    		}
    	}
    
    	$udr->getGroup();
    
    	$group = $udr->getGroup();
    
    	if(isset($_POST["UserDomainRole"])) {
    
    		$form = $_POST["UserDomainRole"];
    
    		$udr->_groupRoleName = $form["_groupRoleName"];
    		
    		$roleDomain = null;
    
    		$alreadyHas = false;
    		$roles = UserDomainRole::find()->where(['domain' => $udr->domain, 'user_id' => $udr->user_id])->all();
    		foreach($roles as $role){
    			if($role->getGroup()->role_name == $udr->_groupRoleName){
    				$alreadyHas = true;
    				break;
    			}
    		}
    		if($alreadyHas){
    			Yii::$app->getSession()->setFlash("warning", Yii::t("aaa", 'The user already has this profile'));
    			return $this->redirect(array('/aaa/user/view', 'id'=>$udr->user_id));
    		}
    		else {
    			if($udr->save()) {
    				AaaNotification::createRole($udr);
                    
                	AaaNotification::deleteRole($udr, $group);
    				 
    				Yii::$app->getSession()->setFlash("success", Yii::t("aaa", 'Role updated successfully'));
    
    				return $this->redirect(array('/aaa/user/view', 'id'=>$udr->user_id));
    			} else {
    				foreach($udr->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->setFlash("error", $error[0]);
    				}
    			}
    		}
    	}
    
    	$groups = [];
    	foreach($udr->getSystemGroupsNoArray() as $group) $groups[$group->role_name] = $group->name;
    
    	return $this->renderPartial('_edit-role-system',array(
    			'udr' => $udr,
    			'groups' => $groups,
    	));
    }
    
    public function actionDelete() {
        if(isset($_POST['delete'])){
            foreach ($_POST['delete'] as $udrId) {
                $udr = UserDomainRole::findOne($udrId);
                $dom = $udr->getDomain();
                $domName = Yii::t("aaa", 'any');
                if($dom) $domName = $dom->name;
                
                if(!self::can("role/delete") && !self::can("user/update")){
                    Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to delete roles'));
                    return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
                }
                
                if(!isset($dom) && !self::can("user/update")){
                	Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to delete roles on domain {domain}',['domain' => $domName]));
                }
                if(!self::can("role/delete", $domName)){
                	Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to delete roles on domain {domain}',['domain' => $domName]));
                }
                else {
                	AaaNotification::deleteRole($udr);
                	
	                $groupType = Group::TYPE_DOMAIN; 
	                $group = $udr->getGroup();
	                if($group) $groupType = $group->type;
	                
	                if ($udr->delete()) {
	                    if($groupType == Group::TYPE_DOMAIN) Yii::$app->getSession()->addFlash('success', Yii::t("aaa", 'The role associated with the domain {name} has been deleted', ['name'=> $domName]));
	                    else Yii::$app->getSession()->addFlash('success', Yii::t("aaa", 'The system role has been deleted'));
	                } else {
	                    if($groupType == Group::TYPE_DOMAIN) Yii::$app->getSession()->setFlash('error', Yii::t("aaa", 'Error deleting the role associated with the domain').' '.$domName);
	                    else Yii::$app->getSession()->addFlash('success', Yii::t("aaa", 'Error deleting the system role'));
	                }
                }
            }
            return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
        }
        return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
    }
    
	public function actionGetGroupsByDomainName(){
    	$name = $_GET['name'];
    	
    	$array = [];
    	
    	if($name){
	    	$groups = UserDomainRole::getDomainGroupsByDomainNoArray($name);
	    	foreach ($groups as $group):
	    		$array[$group->role_name] = $group->name." (".$name.")";
	    	endforeach;
    	}
    	
    	$groups = UserDomainRole::getGlobalDomainGroupsNoArray();
    	foreach ($groups as $group):
    		$array[$group->role_name] = $group->name;
    	endforeach;
    
    	echo json_encode($array);
    }
}
