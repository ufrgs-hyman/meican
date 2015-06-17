<?php

namespace app\modules\aaa\controllers;

use app\modules\aaa\models\UserForm;
use app\modules\aaa\models\AccountForm;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\User;
use app\models\UserSettings;
use app\models\UserDomainRole;
use app\models\Domain;
use app\models\Group;
use app\controllers\RbacController;
use Yii;

class RoleController extends RbacController {
	
    public function actionIndex($id) {
    	self::canRedir("user/read");
    	
    	$user = User::findOne($id);
    	
    	return $this->render('index',array(
    			'user' => $user,
    			'userDomainRoles'=> new ActiveDataProvider([
    					'query' => $user->getUserDomainRoles(),
    					])
    	));
    }
    
    public function actionCreate($id) {
    	self::canRedir("user/update");
    	
    	$udr = new UserDomainRole;
    	$udr->user_id = $id;
    	$domains = $udr->getValidDomains(); 
    	
    	if (count($domains) < 1) {
    		Yii::$app->getSession()->setFlash("warning", Yii::t("aaa", 'This user has all possibles valid roles'));
    		
    		return $this->redirect(array('index', 'id'=>$id));
    	}
    
    	if($udr->load($_POST)) {
    		if($udr->save()) {
    			Yii::$app->getSession()->setFlash("success", Yii::t("aaa", 'Role created successfully'));
    
    			return $this->redirect(array('index', 'id'=>$id));
    
    		} else {
    			foreach($udr->getErrors() as $attribute => $error) {
    				Yii::$app->getSession()->setFlash("error", $error[0]);
    			}
    		}
    	}
    
    	return $this->render('create',array(
    			'udr' => $udr,
    			'groups' => UserDomainRole::getGroups(),
    			'domains' => $domains,
    	));
    }
    
    public function actionUpdate($id) {
    	self::canRedir("user/update");
    	
    	$udr = UserDomainRole::findOne($id);
    	$udr->getGroup();
    	
    	if($udr->load($_POST)) {
    		if($udr->save()) {
    			Yii::$app->getSession()->setFlash("success", Yii::t("aaa", 'Role updated successfully'));
    			 
    			return $this->redirect(array('index', 'id'=>$udr->user_id));
    			 
    		} else {
    			foreach($udr->getErrors() as $attribute => $error) {
    				Yii::$app->getSession()->setFlash("error", $error[0]);
    			}
    		}
    	} 
    	 
    	return $this->render('update',array(
    			'udr' => $udr,
    			'groups' => UserDomainRole::getGroups(),
    			'domains' => $udr->getValidDomains(true)
    	));
    }
    
    public function actionDelete() {
    	self::canRedir("user/update");
    	
    	if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $udrId) {
    			$udr = UserDomainRole::findOne($udrId);
    			$dom = $udr->getDomain();
    			$domName = Yii::t("aaa", 'Any');
    			if ($dom) $domName = $dom->name;
    			if ($udr->delete()) {
    				Yii::$app->getSession()->addFlash('success', Yii::t("aaa", 'The role associated with the domain {name} has been deleted', ['name'=> $domName]));
    			} else {
    				Yii::$app->getSession()->setFlash('error', Yii::t("aaa", 'Error deleting the role associated with the domain ').' '.$domName);
    			}
    		}
    		return $this->redirect(array('index','id'=>$udr->user_id));
    	}
    	
    	return $this->redirect(array('index'));
    }
}
