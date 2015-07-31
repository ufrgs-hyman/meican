<?php

namespace app\modules\aaa\controllers;

use app\modules\aaa\models\UserForm;
use app\modules\aaa\models\AccountForm;
use yii\web\Controller;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use app\models\User;
use app\models\UserSettings;
use app\models\UserDomainRole;
use app\models\Domain;
use app\models\Group;
use app\controllers\RbacController;
use app\models\Notification;
use Yii;

class UserController extends RbacController {
	
	public function actionIndex() {
		if(!self::can("user/read")){ //Se ele não tiver permissão em nenhum domínio
			return $this->goHome();
		}
		
		$dataProvider = new ActiveDataProvider([
				'query' => User::find(),
				]);
		
        return $this->render('index', array(
                'users' => $dataProvider
        ));
    }
    
    public function actionCreate() {
    	if(!self::can("user/create")){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to create users'));
    		return $this->redirect(array('index'));
    	}
    	
    	$userForm = new UserForm;
    
    	if($userForm->load($_POST) && $userForm->validate()) {
    		$user = new User;
    		$user->setFromUserForm($userForm);
    		
    		if($user->save()) {
    			Yii::$app->getSession()->addFlash("success", Yii::t('aaa', 'User added successfully'));
    			
    			Notification::createNotificationsUserNewGroup($user->id, $userForm->group, $userForm->domain);
    			
    			return $this->redirect(array('index'));
    			
    		} else {
    			foreach($user->getErrors() as $attribute => $error) {
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
    			'domains' => self::whichDomainsCan('user/create'),
    			'groups' => UserDomainRole::getGroups()
    	));
    }
    
    public function actionUpdate($id) {
    	if(!self::can("user/update")){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to update users'));
    		return $this->redirect(array('index'));
    	}
    	
    	$user = User::findOne($id);
    	$userForm = new UserForm;
    
    	if($userForm->load($_POST)) {
    		if ($userForm->validate()) {
    			if ($userForm->updateUser($user)) {
    				$settings = $user->getUserSettings()->one();
    				if ($userForm->updateSettings($settings)) {
    					Yii::$app->getSession()->addFlash("success", Yii::t('aaa', 'User updated successfully'));
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
    	
    	//Standard configurations
    	$size = 80; //In pixels
    	$defaultImage = 'mm';
    	$maximumRating = 'g';
    
    	$avatarUrl = "http://www.gravatar.com/avatar/";
    	$avatarUrl .= md5(strtolower(trim("test")));
    	$avatarUrl .= "?s=$size&d=$defaultImage&r=$maximumRating";
    
    	if($account->load($_POST)) {
    		if ($account->validate()) {
    			if ($account->updateUser($user)) {
    				$settings = $user->getUserSettings()->one();
	    			if ($account->updateSettings($settings)) {
	    				$this->redirect(["account", 'lang'=>true]);
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
    			'avatarUrl'=>$avatarUrl,
    			'user'=>$account,
    	));
    }
}
