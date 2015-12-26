<?php

namespace meican\modules\aaa\controllers;

use yii\web\Controller;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;

use meican\modules\aaa\models\UserForm;
use meican\modules\aaa\models\AccountForm;
use meican\modules\aaa\models\UserSearch;

use meican\models\Preference;
use meican\models\User;
use meican\models\UserSettings;
use meican\models\UserDomainRole;
use meican\models\Domain;
use meican\models\Group;
use meican\models\Notification;

use meican\modules\aaa\controllers\RbacController;

use Yii;

class UserController extends RbacController {
	
	public function actionIndex() {
		if(self::can("user/read")){
			$allowedDomains = Domain::find()->orderBy(['name' => SORT_ASC])->all();
			$searchModel = new UserSearch;
			$data = $searchModel->searchByDomains(Yii::$app->request->get(), $allowedDomains, true);
		}
		else if(self::can("role/read")){
			$allowedDomains = self::whichDomainsCan('role/read');
			$searchModel = new UserSearch;
			$data = $searchModel->searchByDomains(Yii::$app->request->get(), $allowedDomains, false);
		}
		else return $this->goHome();

		return $this->render('index', array(
				'searchModel' => $searchModel,
				'users' => $data,
				'domains' => $allowedDomains,
		));

    }
    
    public function actionCreate() {
    	if(!self::can("user/create")){
    		if(!self::can("user/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to create users'));
    			return $this->redirect(array('index'));
    		}
    	}
    	
    	$userForm = new UserForm;
    
    	if($userForm->load($_POST) && $userForm->validate()) {
    		$user = new User;
    		$errors = $user->setFromUserForm($userForm);
    		if(!$errors){
	    		if($user->save()) {
	    			Yii::$app->getSession()->addFlash("success", Yii::t('aaa', 'User added successfully'));
	    			
	    			Notification::createNotificationsUserNewGroup($user->id, $userForm->group, $userForm->domain);
	    			
	    			return $this->redirect(array('index'));
	    		} else {
	    			foreach($user->getErrors() as $attribute => $error) {
	    				Yii::$app->getSession()->addFlash("error", $error[0]);
	    			}
	    		}
    		}
    		else {
    			foreach($errors as $attribute => $error) {
    				Yii::$app->getSession()->addFlash("error", $error[0]);
    			}
    		}
    	} else {
    		foreach($userForm->getErrors() as $attribute => $error) {
    			Yii::$app->getSession()->addFlash("error", $error[0]);
    		}
    		$userForm->clearErrors();
    	}

    	return $this->render('create',array(
    			'user' => $userForm,
    	));
    }
    
    public function actionUpdate($id) {
    	if(!self::can("user/update")){
    		if(!self::can("user/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to update users'));
    			return $this->redirect(array('index'));
    		}
    	}
    	
    	$user = User::findOne($id);
    	
    	if(!$user){
    		if(!self::can("user/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'User not found'));
    			return $this->redirect(array('index'));
    		}
    	}
    	
    	$userForm = new UserForm;
    
    	if($userForm->load($_POST)) {
    		if ($userForm->validate()) {
    			if ($userForm->updateUser($user)) {
    				$settings = $user->getUserSettings()->one();
    				if ($userForm->updateSettings($settings)) {
    					Yii::$app->getSession()->addFlash("success", Yii::t('aaa', 'User updated successfully'));
    					return $this->redirect(array('index'));
    				} else {
    					foreach($settings->getErrors() as $attribute => $error) {
    						Yii::$app->getSession()->addFlash("error", $error[0]);
    					}
    				}
    			} else {
    				foreach($user->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    			}
    		}
    		else {
    			foreach($userForm->getErrors() as $attribute => $error) {
    				Yii::$app->getSession()->addFlash("error", $error[0]);
    			}
    		}
    		
    	} else {
    		$userForm->setFromRecord($user);
    	}
    	
    	$userForm->clearErrors();
    
    	return $this->render('update',array(
    			'user' => $userForm,
    	));
    }
    
    public function actionDelete() {
    	if(!self::can("user/delete")){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to delete users'));
    		return $this->redirect(array('index'));
    	}
    	
    	if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $userId) {
    			$user = User::findOne($userId);
    			if ($user->delete()) {
    				Yii::$app->getSession()->addFlash('success', Yii::t('aaa', 'User {user} deleted successfully', ['user'=>$user->login]));
    			} else {
    				Yii::$app->getSession()->addFlash('error', Yii::t('aaa', 'Error deleting user').' '.$user->login);
    			}
    		}
    	}
    	 
    	return $this->redirect(array('index'));
    }
    
    public function actionAccount($lang=null) {
		if ($lang) {
			Yii::$app->getSession()->addFlash("success", Yii::t('aaa', 'User settings updated successfully'));
		}    	
    	
    	$userId = Yii::$app->user->id;
    
    	$user = User::findOne($userId);
		$account = new AccountForm;
    	
    	if($account->load($_POST)) {
    		if ($account->validate()) {
    			if ($account->updateUser($user)) {
	    				$this->redirect(["account", 'lang'=>true]);
    			} else {
    				foreach($user->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    			}
    		}
    		else {
    			foreach($account->getErrors() as $attribute => $error) {
    				Yii::$app->getSession()->addFlash("error", $error[0]);
    			}
    			$account->clearErrors();
    		}
    
    	} else {
    		$account = new AccountForm;
    		$account->setFromRecord($user);
    	}
    
    	$account->clearPass();
    	
    	return $this->render('account', array(
    			'user'=>$account,
    	));
    }
}
