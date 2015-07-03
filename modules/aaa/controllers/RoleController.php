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
use app\models\Notification;
use Yii;

class RoleController extends RbacController {
	
	const NOTICE_TYPE_ADD_GROUP = 	"ADD_GROUP";
	const NOTICE_TYPE_DEL_GROUP = 	"DEL_GROUP";
	
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
    			//Cria notificações relativas ao novo papel
    			Notification::createNotificationsUserNewGroup($udr->user_id, $udr->_groupRoleName, $udr->domain_id);
    			
    			//Cria notificação de novo papel
    			$domain = Domain::findOne($udr->domain_id);
    			if($domain) Notification::createNoticeNotification($udr->user_id, self::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id, $domain->topology);
    			else Notification::createNoticeNotification($udr->user_id, self::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id);
    			
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

    	$group = $udr->getGroup();
    	$domain_id = $udr->domain_id;
    	
    	if($udr->load($_POST)) {
    		if($udr->save()) {
    			//Remove notificações relativas ao antigo papel
    			Notification::deleteNotificationsUserGroup($udr->user_id, $group->role_name, $domain_id);
    			//Cria notificações relativas ao novo papel
    			Notification::createNotificationsUserNewGroup($udr->user_id, $udr->_groupRoleName, $udr->domain_id);
    			
    			//Pequena maquipulação do horário para que não fiquem as duas notificações com o mesmo horário.
    			$dateAux = new \DateTime('now', new \DateTimeZone("UTC"));
    			$dateAux->modify('-1 second');
    			$dateAux = $dateAux->format("Y-m-d H:i:s");
    			
    			//Cria notificação do papel removido
    			$domain = Domain::findOne($domain_id);
    			if($domain) Notification::createNoticeNotification($udr->user_id, self::NOTICE_TYPE_DEL_GROUP, $group->id, $domain->topology, $dateAux);
    			else Notification::createNoticeNotification($udr->user_id, self::NOTICE_TYPE_DEL_GROUP, $group->id, null, $dateAux);
    			
    			//Cria notificação do novo papel
    			$domain = Domain::findOne($udr->domain_id);
    			if($domain) Notification::createNoticeNotification($udr->user_id, self::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id, $domain->topology);
    			else Notification::createNoticeNotification($udr->user_id, self::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id);
    			 
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
    	self::canRedir("user/delete");
    	
    	if(isset($_POST['delete'])){
    		$date = new \DateTime('now', new \DateTimeZone("UTC"));
    		foreach ($_POST['delete'] as $udrId) {
    			$udr = UserDomainRole::findOne($udrId);
    			$dom = $udr->getDomain();
    			$domName = Yii::t("aaa", 'Any');
    			if ($dom) $domName = $dom->name;

    			//Remove notificações relativas ao antigo papel
    			Notification::deleteNotificationsUserGroup($udr->user_id, $udr->getGroup()->role_name, $udr->domain_id);
    			
    			//Pequena maquipulação do horário para que não fiquem as duas notificações com o mesmo horário.
    			$date->modify('-1 second');
    			$dateAux = $date->format("Y-m-d H:i:s");
    			
    			//Notificação removido papel
    			$domain = Domain::findOne($udr->domain_id);
    			if($domain) Notification::createNoticeNotification($udr->user_id, self::NOTICE_TYPE_DEL_GROUP, $udr->getGroup()->id, $domain->topology, $dateAux);
    			else Notification::createNoticeNotification($udr->user_id, self::NOTICE_TYPE_DEL_GROUP, $udr->getGroup()->id, null, $dateAux);

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
