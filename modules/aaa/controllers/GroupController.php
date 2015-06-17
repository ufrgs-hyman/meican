<?php

namespace app\modules\aaa\controllers;

use app\controllers\RbacController;
use yii\data\ActiveDataProvider;
use app\models\Group;
use yii\web\Controller;
use Yii;

class GroupController extends RbacController {
	
    public function actionIndex() {
    	self::canRedir("read");
    	
    	$dataProvider = new ActiveDataProvider([
    			'query' => Group::find(),
    	]);
    	
        return $this->render('index', array(
           		'groups' => $dataProvider
        ));
    }
    
    public function actionCreate() {
    	self::canRedir("create");
    	
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
    	self::canRedir("update");
    	
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
    	self::canRedir("delete");
    	
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
