<?php

namespace meican\aaa\controllers;

use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use Yii;

use meican\base\models\Preference;
use meican\aaa\forms\UserForm;
use meican\aaa\forms\AccountForm;
use meican\aaa\forms\UserSearch;
use meican\aaa\models\User;
use meican\aaa\models\UserSettings;
use meican\aaa\models\UserDomainRole;
use meican\aaa\models\Group;
use meican\aaa\RbacController;
use meican\notification\models\Notification;
use meican\topology\models\Domain;

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

    public function actionView($id) {
        $user = User::findOne($id);
        
        $rolesProvider = new ActiveDataProvider([
                'query' => $user->getRoles(),
                'pagination' => [
                  'pageSize' => 10,
                ],
                'sort' => false,
        ]);
        
        return $this->render('view', array(
                'model' => $user,
                'rolesProvider' => $rolesProvider
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
        $userForm->scenario = UserForm::SCENARIO_CREATE;
    
        if($userForm->load($_POST) && $userForm->validate()) {
            $user = new User;
            
            if($userForm->createUser($user)){
                Yii::$app->getSession()->addFlash("success", Yii::t('aaa', 'User added successfully'));
                
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
        }

        return $this->render('create',array(
                'user' => $userForm,
        ));
    }

    public function actionUpdateMyAccount() {
        $user = User::findOne(Yii::$app->user->id);
        $userForm = new UserForm;
        $userForm->scenario = UserForm::SCENARIO_UPDATE_ACCOUNT;
        return $this->edit($user, $userForm);
    }

    public function actionUpdate($id) {
        $user = User::findOne($id);
        $userForm = new UserForm;
        $userForm->scenario = UserForm::SCENARIO_UPDATE;
        return $this->edit($user, $userForm);
    }
    
    private function edit($user, $userForm) {
        /*if(!self::can("user/update")){
            if(!self::can("user/read")) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to update users'));
                return $this->redirect(array('index'));
            }
        }
        
        if(!$user){
            if(!self::can("user/read")) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'User not found'));
                return $this->redirect(array('index'));
            }
        }*/
        
        if($userForm->load($_POST)) {
            if ($userForm->validate()) {
                if ($userForm->updateUser($user)) {
                    Yii::$app->getSession()->addFlash("success", Yii::t('aaa', 'User updated successfully'));
                    return $this->redirect(array('index'));
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

    public function actionAccount() {
        $user = User::findOne(Yii::$app->user->id);

        $rolesProvider = new ActiveDataProvider([
                'query' => $user->getRoles(),
                'pagination' => [
                  'pageSize' => 10,
                ],
                'sort' => false,
        ]);
        
        return $this->render('account', array(
                'model' => $user,
                'rolesProvider' => $rolesProvider
        ));
    }
}
