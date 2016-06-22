<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\models;

use Yii;
use yii\helpers\Html;

use meican\base\utils\DateUtils;

use meican\circuits\models\ConnectionAuth;
use meican\circuits\models\Connection;
use meican\circuits\models\Reservation;

use meican\notify\models\Notification;

class ReservationNotification {
	
	static function makeHtml($notification = null){
		if($notification == null) return "";
        $reservation = Reservation::findOne($notification->info);
        if(!$reservation) return "";

        $connection = Connection::find()->where(['reservation_id' => $reservation->id])->one();
        $source = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => 0])->domain;
        $path_order = ConnectionPath::find()->where(['conn_id' => $connection->id])->count()-1;
        $destination = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => $path_order])->domain;
         
        $title = Yii::t("notify", 'Reservation')." (".$reservation->name.")";
        
        $connections = Connection::find()->where(['reservation_id' => $reservation->id])->all();
        
        //Se possui apenas uma conexão, então pode informar diretamente se foi aceito ou negado
        if(count($connections)<2){
            $msg = Yii::t("notify", 'The connection between')." <b>".$source." </b>".Yii::t("notify", 'and')." <b>".$destination."</b> ";
            $date = Yii::$app->formatter->asDatetime($notification->date);
            $link = '/circuits/reservation/view?id='.$reservation->id;
            
            if($connections[0]->status == Connection::STATUS_FAILED_CREATE ||
               $connections[0]->status == Connection::STATUS_FAILED_CONFIRM ||
               $connections[0]->status == Connection::STATUS_FAILED_SUBMIT ||
               $connections[0]->status == Connection::STATUS_FAILED_PROVISION ||
               $connections[0]->status == Connection::STATUS_CANCELLED ||
               $connections[0]->status == Connection::STATUS_CANCEL_REQ ||    
               $connections[0]->auth_status == Connection::AUTH_STATUS_REJECTED ||
               $connections[0]->auth_status == Connection::AUTH_STATUS_EXPIRED
            ){
                $msg .= " ".Yii::t("notify", 'can not be provisioned.');
                $html = Notification::makeHtml('circuit_reject.png', $date, $title, $msg, $link);
            }
            else{
                $msg .= " ".Yii::t("notify", 'was provisioned.');
                $html = Notification::makeHtml('circuit_accept.png', $date, $title, $msg, $link);
            }
        }
        
        //Se possui mais, informa um resumo do status atual da reserva
        else{
            //Conta o número de provisionadas, rejeitadas (aglomera todos estados em que não vai ser gerado o circuito)
            //e pendentes (aglomera todos estados de processamento intermediário)
            $provisioned = 0; $reject = 0; $pending = 0;
            foreach($connections as $conn){
                if($conn->status == Connection::STATUS_PROVISIONED) $provisioned++;
                else if($conn->status == Connection::STATUS_FAILED_CREATE ||
                        $conn->status == Connection::STATUS_FAILED_CONFIRM ||
                        $conn->status == Connection::STATUS_FAILED_SUBMIT ||
                        $conn->status == Connection::STATUS_FAILED_PROVISION ||
                        $conn->status == Connection::STATUS_CANCELLED ||
                        $conn->status == Connection::STATUS_CANCEL_REQ ||
                        $conn->auth_status == Connection::AUTH_STATUS_REJECTED ||
                        $conn->auth_status == Connection::AUTH_STATUS_EXPIRED
                        ) $reject++;
                else $pending++;
            }

            $msg = Yii::t("notify", 'The status of connections changed:')."<br />";
            $msg .= Yii::t("notify", 'Provisioned:')." ".$provisioned.", ";
            $msg .= Yii::t("notify", 'Rejected:')." ".$reject.", ";
            $msg .= Yii::t("notify", 'Pending:')." ".$pending;
            
            $date = Yii::$app->formatter->asDatetime($notification->date);
            $link = '/circuits/reservation/view?id='.$reservation->id;

            $html = Notification::makeHtml('circuit_changed.png', $date, $title, $msg, $link);
        }
        
        if($notification->viewed == true) return '<li>'.$html.'</li>';
        return '<li class="notification_new">'.$html.'</li>';
	}
	
	static function create($connection_id){
        $connection = Connection::findOne($connection_id);
        if(!$connection) return;
        $reservation = Reservation::findOne($connection->reservation_id);
        if(!$reservation) return;
        if($reservation->type == Reservation::TYPE_TEST) return;
        
        $user_id = $reservation->request_user_id;
        
        //Confere se já foi feita uma notificação de algum circuito desta reserva, se sim, reutiliza a mesma notificação
        $not = null;
        $notifications = Notification::find()->where(['user_id' => $reservation->request_user_id, 'type' => Notification::TYPE_RESERVATION])->all();
        foreach($notifications as $notification){
            if($notification->info == $reservation->id){
                $not = $notification;
                break;
            }
        }
        
        if($not){ //Se já existe, atualiza e coloca nova data
			//Pequena maquipulação do horário para que nunca existam duas notificações com o mesmo horário
			$date = new \DateTime('now', new \DateTimeZone("UTC"));
			$dateAux = $date->format("Y-m-d H:i:s");
			while(Notification::find()->where(['user_id' => $user_id, 'date' => $dateAux])->one()){
				$date->modify('-1 second');
				$dateAux = $date->format("Y-m-d H:i:s");
			}
			$not->date = $dateAux;
            $not->viewed = 0;
            $not->save();
        }
        else{ //Se for nova, cria notificação
            $not = new Notification();
            $not->user_id = $reservation->request_user_id;
            //Pequena maquipulação do horário para que nunca existam duas notificações com o mesmo horário
			$date = new \DateTime('now', new \DateTimeZone("UTC"));
			$dateAux = $date->format("Y-m-d H:i:s");
			while(Notification::find()->where(['user_id' => $user_id, 'date' => $dateAux])->one()){
				$date->modify('-1 second');
				$dateAux = $date->format("Y-m-d H:i:s");
			}
			$not->date = $dateAux;
            $not->type = Notification::TYPE_RESERVATION;
            $not->viewed = 0;
            $not->info = (string) $reservation->id;
            $not->save();
        }
	}
	
}