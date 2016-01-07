<?php

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
use meican\notification\models\Notification;

class RoleController extends RbacController {
    
    public function actionIndex($id) {
        if(!self::can("role/read") && !self::can("user/update")){
            return $this->goHome();
        }

        $user = User::findOne($id);
        
        if(!$user){
            if(!self::can("user/read") && !self::can("role/read")) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'User not found'));
                return $this->redirect(array('/aaa/user/index'));
            }
        }
        
        if(self::can("user/read"))
            $roles = $user->getUserDomainRoles();
        else{
            $domainNames = [];
            foreach(self::whichDomainsCan("role/read") as $domain) $domainNames[] = $domain->name;
            $roles = UserDomainRole::find()->where(['in', 'domain', $domainNames])->andWhere(['user_id' => $id]);
        }

        $dataProvider = new ActiveDataProvider([
                'query' => $roles,
                'sort' => false,
                'pagination' => [
                        'pageSize' => 15,
                ],
        ]);
        
        return $this->render('index',array(
                'user' => $user,
                'userDomainRoles'=> $dataProvider,
        ));
    }

     public function actionCreate($id) {
        if(!self::can("role/create") && !self::can("user/update")){
            if(!self::can("user/read") && !self::can("role/read")) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to create roles'));
                return $this->redirect(array('/aaa/user/view', 'id'=>$id));
            }
        }

        $udr = new UserDomainRole;
        $udr->user_id = $id;
        $domains = self::whichDomainsCan('role/create');

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
            }
            else {
                if($udr->save()) {
                    /*
                    //Cria notificações relativas ao novo papel
                    Notification::createNotificationsUserNewGroup($udr->user_id, $udr->_groupRoleName, $udr->domain);
                    
                    //Cria notificação de novo papel
                    $domain = Domain::findOne(['name' => $udr->domain]);
                    if($domain) Notification::createNoticeNotification($udr->user_id, Notification::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id, $domain->name);
                    else Notification::createNoticeNotification($udr->user_id, Notification::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id);
                    */
                    
                    Yii::$app->getSession()->setFlash("success", Yii::t("aaa", 'Role created successfully'));

                    return $this->redirect(array('/aaa/user/view', 'id'=>$id));
        
                } else {
                    foreach($udr->getErrors() as $attribute => $error) {
                        Yii::$app->getSession()->setFlash("error", $error[0]);
                    }
                }
            }
        }
        
        $systemGroups = [];
        $anyDomain = [];
        if(self::can("user/update")){
            $groups = [];
            foreach($udr->getGroupsNoArray() as $group){
                $groups[$group->role_name] = $group->name." (".$group->getType().")";
            }
            
            $anyDomain = [null=>Yii::t("aaa" , "any")];
            $domains = Domain::find()->orderBy(['name' => SORT_ASC])->asArray()->all();
            $sysGroups = $udr->getSystemGroupsNoArray();
            foreach($sysGroups as $g){
                $systemGroups[] = $g->role_name;
            }
        }
        else{
            $groups = [];
            foreach($udr->getDomainGroupsNoArray() as $group){
                $groups[$group->role_name] = $group->name;
            }
        }
    
        return $this->renderPartial('_add-role-form',array(
                'udr' => $udr,
                'groups' => $groups,
                'domains' => $domains,
                'systemGroups' => $systemGroups,
                'anyDomain' => $anyDomain,
        ));
    }

    public function actionUpdate($id) {
        $udr = UserDomainRole::findOne($id);

        if(!$udr){
            if(!self::can("user/read") && !self::can("role/read")) return $this->goHome();
            else{
                Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Role not found'));
                return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
            }
        }
        if(!self::can("role/update") && !self::can("user/update")){
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
            }
            else {
                if($udr->save()) {
                    /*
                    //Remove notificações relativas ao antigo papel
                    Notification::deleteNotificationsUserGroup($udr->user_id, $group->role_name, $udr->domain);
                    //Cria notificações relativas ao novo papel
                    Notification::createNotificationsUserNewGroup($udr->user_id, $udr->_groupRoleName, $udr->domain);
                    
                    //Pequena maquipulação do horário para que não fiquem as duas notificações com o mesmo horário.
                    $dateAux = new \DateTime('now', new \DateTimeZone("UTC"));
                    $dateAux->modify('-1 second');
                    $dateAux = $dateAux->format("Y-m-d H:i:s");
                    
                    //Cria notificação do papel removido
                    $domain = Domain::findOne(['name' => $udr->domain]);
                    if($domain) Notification::createNoticeNotification($udr->user_id, Notification::NOTICE_TYPE_DEL_GROUP, $group->id, $domain->name, $dateAux);
                    else Notification::createNoticeNotification($udr->user_id, Notification::NOTICE_TYPE_DEL_GROUP, $group->id, null, $dateAux);
                    
                    //Cria notificação do novo papel
                    $domain = Domain::findOne(['name' => $udr->domain]);
                    if($domain) Notification::createNoticeNotification($udr->user_id, Notification::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id, $domain->name);
                    else Notification::createNoticeNotification($udr->user_id, Notification::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id);
                     
                    */
                	
                    Yii::$app->getSession()->setFlash("success", Yii::t("aaa", 'Role updated successfully'));
                    
                    return $this->redirect(array('/aaa/user/view', 'id'=>$udr->user_id));
                     
                } else {
                    foreach($udr->getErrors() as $attribute => $error) {
                        Yii::$app->getSession()->setFlash("error", $error[0]);
                    }
                }
            }
        }
        
        $systemGroups = [];
        $anyDomain = [];
        if(self::can("user/update")){
            $groups = [];
            foreach($udr->getGroupsNoArray() as $group){
                $groups[$group->role_name] = $group->name." (".$group->getType().")";
            }
            
            $anyDomain = [null=>Yii::t("aaa" , "any")];
            $domains = Domain::find()->orderBy(['name' => SORT_ASC])->asArray()->all();
            $sysGroups = $udr->getSystemGroupsNoArray();
            foreach($sysGroups as $g){
                $systemGroups[] = $g->role_name;
            }
        }
        else{
            if(!self::can("role/update", $udr->domain)){
                Yii::$app->getSession()->setFlash("warning", Yii::t("aaa", 'You are not allowed to update this role'));
                return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
            }
            else if($udr->domain == null && !self::can("user/update")){
                Yii::$app->getSession()->setFlash("warning", Yii::t("aaa", 'You are not allowed to update this role'));
                return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
            }
            $groups = [];
            foreach($udr->getDomainGroupsNoArray() as $group){
                $groups[$group->role_name] = $group->name;
            }
        }

        return $this->renderPartial('_edit-role-form',array(
                'udr' => $udr,
                'groups' => $groups,
                'domains' => $domains,
                'systemGroups' => $systemGroups,
                'anyDomain' => $anyDomain,
        ));
    }
    
    public function actionDelete() {
        if(isset($_POST['delete'])){
            $date = new \DateTime('now', new \DateTimeZone("UTC"));
            foreach ($_POST['delete'] as $udrId) {
                $udr = UserDomainRole::findOne($udrId);
                
                if(!self::can("role/delete") && !self::can("user/update")){
                    Yii::$app->getSession()->addFlash('warning', Yii::t('aaa', 'You are not allowed to delete roles'));
                    return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
                }
                
                $dom = $udr->getDomain();
                $domName = Yii::t("aaa", 'any');
                if ($dom) $domName = $dom->name;
                
                /*
                //Remove notificações relativas ao antigo papel
                $domain = Domain::findOne(['name' => $udr->domain]);
                if($domain) Notification::deleteNotificationsUserGroup($udr->user_id, $udr->getGroup()->role_name, $domain->name);
                else Notification::deleteNotificationsUserGroup($udr->user_id, $udr->getGroup()->role_name, null);
                
                //Pequena maquipulação do horário para que não fiquem as duas notificações com o mesmo horário.
                $date->modify('-1 second');
                $dateAux = $date->format("Y-m-d H:i:s");
                
                //Notificação removido papel
                $domain = Domain::findOne(['name' => $udr->domain]);
                if($domain) Notification::createNoticeNotification($udr->user_id, Notification::NOTICE_TYPE_DEL_GROUP, $udr->getGroup()->id, $domain->name, $dateAux);
                else Notification::createNoticeNotification($udr->user_id, Notification::NOTICE_TYPE_DEL_GROUP, $udr->getGroup()->id, null, $dateAux);
				*/

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
            return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
        }
        return $this->redirect(array('/aaa/user/view','id'=>$udr->user_id));
    }
}
