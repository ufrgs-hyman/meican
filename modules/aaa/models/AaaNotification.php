<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\aaa\models;

use Yii;
use yii\helpers\Html;

use meican\aaa\models\UserDomainRole;
use meican\aaa\models\User;
use meican\aaa\models\Group;

use meican\base\components\DateUtils;

use meican\circuits\models\ConnectionAuth;
use meican\circuits\models\Connection;
use meican\circuits\models\Reservation;
use meican\circuits\models\AuthorizationNotification;

use meican\notification\models\Notification;

use meican\topology\models\Domain;

class AaaNotification {
	
	static function makeHtml($notification = null){
		if($notification == null) return "";
		$data = json_decode($notification->info);
		$type = $data[0];
		switch($type){
			//ADICIONADO A UM GRUPO
			case Notification::NOTICE_TYPE_ADD_GROUP:
				$group = Group::findOne($data[1]);
				if(!$group) return "";
				//Se não possui dado extra é para todos, do contrario, possui dominio
				if(isset($data[2])){
					$domain = Domain::findOne(['name' => $data[2]]);
					if(!$domain) return "";
				}
				 
				$title = Yii::t("notification", 'Added to a group');
				 
				$msg = Yii::t("notification", 'You have been added to group')." <b>".$group->name."</b>";
				if($group->type==Group::TYPE_DOMAIN){
					if(isset($domain)) $msg .= " ".Yii::t("notification", 'of the domain')." <b>".$domain->name."</b>.";
					else if(!isset($data[2])) $msg .= " ".Yii::t("notification", 'of all domains.');
					else $msg .= ".";
				}
				else {
					$msg .= " ".Yii::t("notification", 'with system permissions');
				}
				 
				$date = Yii::$app->formatter->asDatetime($notification->date);
				 
				$html = Notification::makeHtml('notice.png', $date, $title, $msg);
				break;
				 
			//REMOVIDO DE UM GRUPO
			case Notification::NOTICE_TYPE_DEL_GROUP:
				$group = Group::findOne($data[1]);
				if(!$group) return "";
				//Se não possui dado extra é para todos, do contrario, possui dominio
				if(isset($data[2])){
					$domain = Domain::findOne(['name' => $data[2]]);
					if(!$domain) return "";
				}
				 
				$title = Yii::t("notification", 'Removed from a group');
	
				$msg = Yii::t("notification", 'You were removed from the group')." <b>".$group->name."</b>";
				if($group->type==Group::TYPE_DOMAIN){
					if(isset($domain)) $msg .= " ".Yii::t("notification", 'of the domain')." <b>".$domain->name."</b>.";
					else if(!isset($data[2])) $msg .= " ".Yii::t("notification", 'of all domains.');
					else $msg .= ".";
				}
				else {
					$msg .= " ".Yii::t("notification", 'with system permissions');
				}
	
				$date = Yii::$app->formatter->asDatetime($notification->date);
	
				$html = Notification::makeHtml('notice.png', $date, $title, $msg);
				break;
		}
	
		if($notification->viewed == true) return '<li>'.$html.'</li>';
		return '<li class="notification_new">'.$html.'</li>';
	}
	
	static function create($user_id, $type, $group, $domain = null, $date = null){
		$notice = [];
		$notice[0] = $type; //Tipo da noticia
		$notice[1] = $group;
		if($domain) $notice[2] = $domain;
	
		$not = new Notification();
		$not->user_id = $user_id;
		//Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
		if($date) $not->date = $date;
		else{
			//Pequena maquipulação do horário para que nunca existam duas notificações com o mesmo horário
			$date = new \DateTime('now', new \DateTimeZone("UTC"));
			$dateAux = $date->format("Y-m-d H:i:s");
			while(Notification::find()->where(['user_id' => $user_id, 'date' => $dateAux])->one()){
				$date->modify('-1 second');
				$dateAux = $date->format("Y-m-d H:i:s");
			}
			$not->date = $dateAux;
		}
		$not->type = Notification::TYPE_NOTICE;
		$not->viewed = 0;
		$not->info = json_encode($notice); //Armazena todos dados em um JSON
		$not->save();
	}
	
	static function createRole($udr){
		//Cria notificações relativas ao novo papel
		AaaNotification::createNotificationsGroup($udr->user_id, $udr->_groupRoleName, $udr->domain);
		
		//Cria notificação de novo papel
		$domain = Domain::findOne(['name' => $udr->domain]);
		if($domain) AaaNotification::create($udr->user_id, Notification::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id, $domain->name);
		else AaaNotification::create($udr->user_id, Notification::NOTICE_TYPE_ADD_GROUP, $udr->getGroup()->id);
	}

	static function deleteRole($udr, $group = null, $domain = null){
		if(!isset($group)) $group = $udr->getGroup();
		if(!isset($group)) $domain = Domain::findOne(['name' => $udr->domain]);
		
		//Remove notificações relativas ao antigo papel
		if($domain){
			AaaNotification::deleteNotificationsGroup($udr->user_id, $group, $domain->name);
		}
		else{
			AaaNotification::deleteNotificationsGroup($udr->user_id, $group, null);
		}

		//Notificação removido papel
		if($domain){
			AaaNotification::create($udr->user_id, Notification::NOTICE_TYPE_DEL_GROUP, $group->id, $domain->name);
		}
		else{
			AaaNotification::create($udr->user_id, Notification::NOTICE_TYPE_DEL_GROUP, $group->id, null);
		}
	}
    
    static function createNotificationsGroup($user_id, $group_name, $domain){
    	$user = User::findOne($user_id);
    	$group = Group::findOne(['role_name' => $group_name]);
    	
    	if($user && $group){
    		Yii::trace("Criar notificações do grupo ".$group->name." para usuário ".$user->name);
    		//Busca todas autorizações pendentes do grupo
    		//Se tem dominio, procura só as relacionadas ao dominio do papel
    		if($domain){
    			$auths = ConnectionAuth::find()->where(['status' => Connection::AUTH_STATUS_PENDING, 'domain' => $domain, 'type' => ConnectionAuth::TYPE_GROUP, 'manager_group_id' => $group->id])->all();
    		}
    		//Se não possui domonio no papel, busca para todos dominios, pois é ANY
    		else $auths = ConnectionAuth::find()->where(['status' => Connection::AUTH_STATUS_PENDING, 'type' => ConnectionAuth::TYPE_GROUP, 'manager_group_id' => $group->id])->all();
    		
    		//Passa por todas criando uma notificação
    		foreach($auths as $auth){
    			$connection = Connection::findOne($auth->connection_id);
    			$reservation = Reservation::findOne($connection->reservation_id);
    			AuthorizationNotification::createToUser($user->id, $auth->domain, $connection->reservation_id, $auth->id, $reservation->date);
    		}
    	}
    }
    
    static function deleteNotificationsGroup($user_id, $group, $domain){
    	$user = User::findOne($user_id);
    
    	if($user && $group){
    		Yii::trace("Remover notificações do grupo ".$group->name." para usuário ".$user->name);
    		//Busca todas autorizações do grupo
    		//Se tem domínio, procura só as relacionadas ao domínio do papel
    		if($domain){
    			$auths = ConnectionAuth::find()->where(['domain' => $domain, 'type' => ConnectionAuth::TYPE_GROUP, 'manager_group_id' => $group->id])->all();
    		}
    		//Se não possui domínio no papel, busca para todos dominios, pois é ANY
    		else $auths = ConnectionAuth::find()->where(['type' => ConnectionAuth::TYPE_GROUP, 'manager_group_id' => $group->id])->all();
    
    		//Passa por todas deletando uma notificação
    		foreach($auths as $auth){
    			$notification = Notification::findOne(['user_id' => $user_id, 'type' => Notification::TYPE_AUTHORIZATION, 'info' => $auth->id]);
    			if($notification) $notification->delete();
    		}
    	}
    }
}