<?php
/**
 * @copyright Copyright (c) 2012-2021 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

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
use meican\notify\models\Notification;
use meican\topology\models\Domain;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
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
            $data = $searchModel->searchByDomains(Yii::$app->request->get(), $allowedDomains, false, true);
        }
        else{
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to edit Users'));
            return $this->goHome();
        } 

        return $this->render('index', array(
                'searchModel' => $searchModel,
                'users' => $data,
                'domains' => $allowedDomains,
        ));
    }

    public function actionView($id) {
        $user = User::findOne($id);

        if(self::can("user/read")){
        	$roles = UserDomainRole::find()->where(['user_id' => $user->id])->all();
        	$filtered = [];
        	foreach($roles as $role){
        		if($role->getGroup()->type == Group::TYPE_DOMAIN) $filtered[] = $role->id;
        	}
        	$queryDomain = UserDomainRole::find()->where(['in', 'id', $filtered]);
        }
        else if(self::can("role/read")){
        	$allowedDomains = self::whichDomainsCan('role/read');
            $domains_name = [];
            foreach($allowedDomains as $domain) $domains_name[] = $domain->name;
            $roles = UserDomainRole::find()->where(['user_id' => $user->id])->andWhere(['in', 'domain', $domains_name])->all();
            $filtered = [];
            foreach($roles as $role){
            	if($role->getGroup()->type == Group::TYPE_DOMAIN) $filtered[] = $role->id;
            }
            $queryDomain = UserDomainRole::find()->where(['in', 'id', $filtered]);
        } else {
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to view users'));
            return $this->goHome();
        }
        $domainProvider = new ActiveDataProvider([
                'query' => $queryDomain,
                'pagination' => [
                  	'pageSize' => 5,
                ],
                'sort' => false,
        ]);

        $roles = UserDomainRole::find()->where(['user_id' => $user->id])->all();
        $filtered = [];
        if(self::can("user/read")){
        	foreach($roles as $role){
        		if($role->getGroup()->type == Group::TYPE_SYSTEM) $filtered[] = $role->id;
        	}
        }
        $querySystem = UserDomainRole::find()->where(['in', 'id', $filtered]);
        $systemProvider = new ActiveDataProvider([
        		'query' => $querySystem,
        		'pagination' => [
        			'pageSize' => 5,
        		],
        		'sort' => false,
        ]);

        return $this->render('view', array(
                'model' => $user,
                'domainRolesProvider' => $domainProvider,
        		'systemRolesProvider' => $systemProvider,
        ));
    }

    public function actionCreate() {
        if(!self::can("user/create")){
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to create users'));
            if(self::can("user/read") || self::can("role/read"))
                return $this->redirect(array('index'));
            else
                return $this->goHome();
        }

        $userForm = new UserForm;
        $userForm->scenario = UserForm::SCENARIO_CREATE;

        if($userForm->load($_POST) && $userForm->validate()) {
            $user = new User;

            if($userForm->createUser($user)){
                Yii::$app->getSession()->addFlash("success", Yii::t('aaa', 'User added successfully'));

                return $this->redirect(array('index'));
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
        $user_id = User::findOne(Yii::$app->user->id)->id;
        
        if($user_id != $id) {
            if(!self::can('user/update')) {
                Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to update users'));
                if(self::can('role/update') || self::can('role/read'))
                    return $this->actionView($id);
                else
                    return $this->goHome();
            }
        }

        $user = User::findOne($id);
        $userForm = new UserForm;
        $userForm->scenario = UserForm::SCENARIO_UPDATE;
        return $this->edit($user, $userForm);
    }

    private function edit($user, $userForm) {
        if($userForm->load($_POST)) {
            if ($userForm->validate()) {
                if ($userForm->updateUser($user)) {
                    Yii::$app->getSession()->addFlash("success", Yii::t('aaa', 'User updated successfully'));
                    return $this->redirect(array('index'));
                }
            }

        } else {
            $userForm->setFromRecord($user);
        }

        return $this->render('update',array(
                'user' => $userForm,
        ));
    }

    public function actionDelete() {
        if(!self::can("user/delete")){
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to delete users'));
            return $this->goHome();
        }

        if(isset($_POST['delete'])){
            foreach ($_POST['delete'] as $userId) {
                $user = User::findOne($userId);
                if ($user->delete())
                    Yii::$app->getSession()->addFlash('success', Yii::t('aaa', 'User {user} deleted successfully', ['user'=>$user->login]));
                else
                    Yii::$app->getSession()->addFlash('error', Yii::t('aaa', 'Error deleting user').' '.$user->login);
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
