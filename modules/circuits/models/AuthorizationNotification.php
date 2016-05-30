<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\models;

use Yii;
use yii\helpers\Html;

use meican\base\components\DateUtils;

use meican\circuits\models\ConnectionAuth;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\Connection;
use meican\circuits\models\Reservation;

use meican\aaa\models\Group;
use meican\aaa\models\UserDomainRole;

use meican\topology\models\Domain;

use meican\notify\models\Notification;

class AuthorizationNotification {
	
	static function makeHtml($notification = null){
        if($notification == null) return "";
        $auth_id = $notification->info;
        if($auth_id == null) return "";
        $auth = ConnectionAuth::findOne($auth_id);
        if(!$auth) return "";

        $connection = Connection::findOne($auth->connection_id);
        
        $source = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => 0])->domain;
        $path_order = ConnectionPath::find()->where(['conn_id' => $connection->id])->count()-1;
        $destination = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => $path_order])->domain;
        
        $reservation = Reservation::findOne($connection->reservation_id);
        
        $title = Yii::t("notify", 'Pending authorization');
        $msg = Yii::t("notify", 'The connection is from')." <b>".$source."</b> ".Yii::t("notify", 'to')." <b>".$destination."</b>";
        $msg .= ". ".Yii::t("notify", 'The request bandwidth is')." ".$reservation->bandwidth." Mbps.";
        $date = Yii::$app->formatter->asDatetime($notification->date);
        
        $link = '/circuits/authorization/answer?id='.$reservation->id.'&domain='.$auth->domain;

        $html = Notification::makeHtml('pending_authorization.png', $date, $title, $msg, $link);
        
        if($notification->viewed == true) return '<li>'.$html.'</li>';
        return '<li class="notification_new">'.$html.'</li>';
    }    
	
	static function createToUser($user_id, $domain, $reservation_id, $auth_id, $date = null){
        //Confere se já foi feita uma notificação de algum circuito desta reserva, se sim, reutiliza a mesma notificação
        $not = null;
        $notifications = Notification::find()->where(['user_id' => $user_id, 'type' => Notification::TYPE_AUTHORIZATION])->all();
        foreach($notifications as $notification){ //Passa por todas notificações
            $cauth = ConnectionAuth::findOne($notification->info);
            if($cauth){
                if($cauth->domain == $domain){
                    $conn = Connection::findOne($cauth->connection_id);
                    if($conn){
                        if($conn->reservation_id == $reservation_id){
                            $not = $notification;
                            break;
                        }
                    }
                }
            }
        }
         
        if($not){ //Se ja existe, atualiza e coloca nova data
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
            $not->viewed = 0;
            $not->save();
        }
        else{ //Se é nova, cria notificação
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
            $not->type = Notification::TYPE_AUTHORIZATION;
            $not->viewed = 0;
            $not->info = (string) $auth_id;
            $not->save();
        }
    }
    
    static function createToGroup($group_id, $domain, $reservation_id, $auth_id, $date = null){
    	$group = Group::findOne($group_id);
    	 
    	$domain = Domain::findOne(['name' => $domain]);
    	 
    	if(!$group || !$domain) return false;
    	 
    	//Confere todos papeis associados ao grupo
    	$roles = UserDomainRole::findByGroup($group);
    	foreach($roles->all() as $role){
    		if($role->domain == null || $role->domain == $domain->name){ //Se papel for para todos dominios ou para dominio espeficido
    			//Confere se já foi feita uma notificação de algum circuito desta reserva, se sim, reutiliza a mesma notificação
    			$not = null;
    			$notifications = Notification::find()->where(['user_id' => $role->user_id, 'type' => Notification::TYPE_AUTHORIZATION])->all();
    			foreach($notifications as $notification){
    				$cauth = ConnectionAuth::findOne($notification->info);
    				if($cauth){
    					if($cauth->domain == $domain->name){
    						$conn = Connection::findOne($cauth->connection_id);
    						if($conn){
    							if($conn->reservation_id == $reservation_id){
    								$not = $notification;
    								break;
    							}
    						}
    					}
    				}
    			}
    
    			if($not){ //Se já existe, atualiza e coloca nova data
	    			//Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
		            if($date) $not->date = $date;
		        	else{
		            	//Pequena maquipulação do horário para que nunca existam duas notificações com o mesmo horário
						$date = new \DateTime('now', new \DateTimeZone("UTC"));
						$dateAux = $date->format("Y-m-d H:i:s");
						while(Notification::find()->where(['user_id' => $role->user_id, 'date' => $dateAux])->one()){
							$date->modify('-1 second');
							$dateAux = $date->format("Y-m-d H:i:s");
						}
						$not->date = $dateAux;
		            }
    				$not->viewed = 0;
    				$not->save();
    			}
    			else{ //Se for nova, cria notificação
    				$not = new Notification();
    				$not->user_id = $role->user_id;
    				if(isset($date)) $not->date = $date;
		        	else{
		            	//Pequena maquipulação do horário para que nunca existam duas notificações com o mesmo horário
						$date = new \DateTime('now', new \DateTimeZone("UTC"));
						$dateAux = $date->format("Y-m-d H:i:s");
						while(Notification::find()->where(['user_id' => $role->user_id, 'date' => $dateAux])->one()){
							$date->modify('-1 second');
							$dateAux = $date->format("Y-m-d H:i:s");
						}
						$not->date = $dateAux;
		            }
					$not->date = $dateAux;
    				$not->type = Notification::TYPE_AUTHORIZATION;
    				$not->viewed = 0;
    				$not->info = (string) $auth_id;
    				$not->save();
    			}
    		}
    	}
    }
    
    static function clearAuthorizations($userId){
	    //Limpa as notificações de autorização que ja foram respondidas
	    $notAuth = Notification::find()->where(['user_id' => $userId, 'type' => Notification::TYPE_AUTHORIZATION, 'viewed' => false])->all();
	    foreach($notAuth as $not){ //Confere todas notificações do tipo autorização
	    	$auth = ConnectionAuth::findOne($not->info);
	    	if($auth){
	    		$connection = Connection::findOne($auth->connection_id);
	    		if($connection){
	    			$connections = Connection::find()->where(['reservation_id' => $connection->reservation_id])->all();
	    			$answered = true;
	    			foreach($connections as $conn){ //Confere todas conexões da reserva conferindo se alguma ainda esta pendente
	    				$auths = ConnectionAuth::find()->where(['domain' => $auth->domain, 'connection_id' => $conn->id])->all();
	    				foreach($auths as $a){ //Confere as autorizaçòes daquela conexão
	    					if($a->type != ConnectionAuth::TYPE_WORKFLOW && $a->status == Connection::AUTH_STATUS_PENDING){
	    						$answered = false;
	    						break;
	    					}
	    				}
	    				if($answered == false) break;
	    			}
	    			if($answered){ //Se ja foi respondida modifica notificação para visualizada
	    				$not->viewed = true;
	    				$not->save();
	    			}
	    		}
	    	}
	    }
    }
	
}