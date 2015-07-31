<?php

namespace app\modules\aaa\controllers;

use app\controllers\RbacController;
use yii\data\ActiveDataProvider;
use app\models\Group;
use yii\web\Controller;
use Yii;

class GroupController extends RbacController {
	
    public function actionIndex() {
    	if(!self::can("group/read")){ //Se ele não tiver permissão em nenhum domínio
			return $this->goHome();
		}
    	
    	$dataProvider = new ActiveDataProvider([
    			'query' => Group::find(),
    	]);
    	
        return $this->render('index', array(
           		'groups' => $dataProvider
        ));
    }
    
    public function actionCreate() {
    	if(!self::can("group/create")){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to create groups'));
    		return $this->redirect(array('index'));
    	}
    	
    	$group = new Group;
    
    	if($group->load($_POST)) {
    		if($group->save()) {
    			if (isset($_POST['Permissions']) && $group->setPermissions($_POST['Permissions'])) {
    				Yii::$app->getSession()->setFlash('success', Yii::t("aaa", 'Group created successfully'));
    			} else {
    				Yii::$app->getSession()->setFlash('warning', Yii::t("aaa", 'Group created. None permissions?'));
    			}
    						
    			return $this->redirect(array('index'));
    					
    		} else {
    			foreach($group->getErrors() as $attribute => $error) {
    				Yii::$app->getSession()->setFlash("error", $error[0]);
    			}
    		}
    	}
    
    	return $this->render('create',array(
    			'group' => $group,
    			'apps' => array(
    					'reservation'=>Yii::t("aaa", 'Reservations'),
    					'workflow'=>Yii::t("aaa", 'Workflows'), 
    					'topology'=>Yii::t("aaa", 'Topologies'),
    					'user'=>Yii::t("aaa", 'Users'),
    					'group'=>Yii::t("aaa", 'Groups')),
    	));
    }
    
    public function actionUpdate($id) {
    	if(!self::can("group/update")){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to update groups'));
    		return $this->redirect(array('index'));
    	}
    	
    	$group = Group::findOne($id);
    	$childsChecked = $group->getPermissions();
    
    	if($group->load($_POST)) {
    		if(isset($_POST['Permissions'])) {
    			if ($group->save() && $group->setPermissions($_POST['Permissions'])) {
    				Yii::$app->getSession()->setFlash('success', Yii::t("aaa", 'Group updated successfully'));
    			} else {
    				Yii::$app->getSession()->setFlash('warning', Yii::t("aaa", 'Group updated successfully. None permissions?'));
    			}
    					
    			return $this->redirect(array('index'));
    		}
    		else {
    			foreach($group->getErrors() as $attribute => $error) {
    				Yii::$app->getSession()->setFlash("error", $error[0]);
    			}
    		}
    	}
    
    	return $this->render('update',array(
    			'group' => $group,
    			'apps' => array(
    					'reservation'=>Yii::t("aaa", 'Reservations'),
    					'workflow'=>Yii::t("aaa", 'Workflows'), 
    					'topology'=>Yii::t("aaa", 'Topologies'),
    					'user'=>Yii::t("aaa", 'Users'),
    					'group'=>Yii::t("aaa", 'Groups')),
    			'childsChecked' => $childsChecked
    	));
    }
    
    public function actionDelete(){
    	if(!self::can("group/delete")){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to delete groups'));
    		return $this->redirect(array('index'));
    	}
    		
    	if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $groupId) {
    			$group = Group::findOne($groupId);
    			if ($group->delete()) {
    				Yii::$app->getSession()->addFlash('success', Yii::t("aaa", 'Group {name} deleted successfully', ['name'=> $group->name]));
    			} else {
    				Yii::$app->getSession()->setFlash('error', Yii::t("aaa", 'Error deleting group').' '.$group->name);
    			}
    		}
    	}
    	 
    	return $this->redirect(array('index'));
    }
}
