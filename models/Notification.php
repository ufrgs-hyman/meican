<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use app\models\Reservation;
use app\models\ReservationPath;
use app\components\DateUtils;

/**
 * This is the model class for table "meican_notification".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $date
 * @property string $type
 * @property integer $viewed
 * @property string $info
 *
 * @property User $id0
 */
class Notification extends \yii\db\ActiveRecord
{	
	const TYPE_AUTHORIZATION = 		"AUTHORIZATION";
	const TYPE_RESERVATION = 		"RESERVATION";
	const TYPE_TOPOLOGY = 			"TOPOLOGY";
	const TYPE_NOTICE = 			"NOTICE";
	
	const NOTICE_TYPE_ADD_GROUP = 	"ADD_GROUP";
	const NOTICE_TYPE_DEL_GROUP = 	"DEL_GROUP";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'date', 'type', 'viewed', 'info'], 'required'],
            [['user_id', 'viewed'], 'integer'],
            [['date'], 'safe'],
            [['type'], 'string'],
            [['info'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'type' => 'Type',
            'viewed' => 'Viewed',
            'info' => 'Info',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getId0()
    {
        return $this->hasOne(User::className(), ['id' => 'id']);
    }
    
    public static function makeHtml($image, $text){
    	return '<table><tr><td class="image">'.Html::img('@web'.'/images/'.$image).'</td><td>'.$text.'</td></tr></table>';
    }
    
    public static function getNumberNotifications(){
    	$nots = 0;
    
    	if(Yii::$app->user->isGuest) return $auths;
    
    	$userId = Yii::$app->user->getId();
    
    	return Notification::find()->where(['user_id' => $userId, 'viewed' => 0])->count();
    }
    
    public static function getNumberAuthorizations(){
    	$auths = 0;
    
    	if(Yii::$app->user->isGuest) return $auths;
    	 
    	$userId = Yii::$app->user->getId();
    
    	return Notification::find()->where(['user_id' => $userId, 'viewed' => 0, 'type' => self::TYPE_AUTHORIZATION])->count();
    }
    
    /**
     * GET NOTIFICATIONS
     * @param string $dateParam
     * @return string
     * Retorna o html com até 6 notificações, ja formatado para ser exibido.
     * Quando recebe uma data de entrada, a utiliza como limite, e retorna apenas o que vem depois dela
     */
    public static function getNotifications($dateParam){    	
    	$userId = Yii::$app->user->getId();

    	if(!$dateParam){ //Caso seja a primeira solicitação  	
    		//Limpa as notificações de autorização que ja foram respondidas
    		$notAuth = Notification::find()->where(['user_id' => $userId, 'type' => self::TYPE_AUTHORIZATION, 'viewed' => false])->all();
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

    	$array = "";
    	$max = 0;
    	$date=null;
    	
    	//Le todas reservas anteriores a data limite, ou todas reservas, caso não exista uma data limite
    	if($dateParam) $notifications = Notification::find()->where(['user_id' => $userId])->andWhere(['<','date', $_POST['date']])->orderBy(['date' => SORT_DESC])->all();
    	else $notifications = Notification::find()->where(['user_id' => $userId])->orderBy(['date' => SORT_DESC])->all();
    	
    	//Se não contem, gera aviso de que o usuário não possui notificações
    	if(count($notifications) == 0){
    		$info = [];
    		$info['date'] = null;
    		$info['array'] = "<li style='text-align: center;'><span style='float: none !important;'><h2>".Yii::t("notification", 'You don`t have notifications yet.')."</h2></span></li>";
    		$info['more'] = false;
    		return $info;
    	}
    	
    	//Percorre as notificações gerando o HTML
    	foreach($notifications as $notification){
    		if($max<6){
    			$msg = "";
	    		switch ($notification->type) {
	    			case self::TYPE_AUTHORIZATION:
	    				$msg = Notification::makeHtmlNotificationAuth($notification);
	    				break;
	    			case self::TYPE_RESERVATION:
	    				$msg = Notification::makeHtmlNotificationReservation($notification);
	    				$notification->viewed = true;
	    				$notification->save();
	    				break;
	    			case self::TYPE_NOTICE:
	    				$msg = Notification::makeHtmlNotificationNotice($notification);
	    				$notification->viewed = true;
	    				$notification->save();
	    				break;
	    			case self::TYPE_TOPOLOGY:
	    				$msg = Notification::makeHtmlNotificationTopology($notification);
	    				$notification->viewed = true;
	    				$notification->save();
	    				break;
	    		}
	    		$array .= $msg;
	    		$date = $notification->date;
    		}
    		$max++;
    		if($max == 7){
    			break;
    		}
    	}
    	
    	$info = [];
    	$info['date'] = $date; //Data da ultima notificação retornada, utilizada como limite para ler as proximas em leituras futuras
    	$info['array'] = $array; //HTML a ser exibido
    	if($max == 7) $info['more'] = true; //Flag para informar ao JS se existem mais notificações
    	else $info['more'] = false;

    	return $info;
    }

    /********************************
     *
     * CREATE NOTIFICATION
     *
     ********************************/
    
    /**
     * CREATE USER AUTH NOTIFICATION
     * @param string $user_id
     * @param string $domain
     * @param string $reservation_id
     * @param string $auth_id
     * @param string $date
     * Cria notificação sobre pedido de autorização para usuário
     */
    public static function createUserAuthNotification($user_id, $domain, $reservation_id, $auth_id, $date = null){
	    //Confere se já foi feita uma notificação de algum circuito desta reserva, se sim, reutiliza a mesma notificação
    	$not = null;
	    $notifications = Notification::find()->where(['user_id' => $user_id, 'type' => self::TYPE_AUTHORIZATION])->all();
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
	    	else $not->date = DateUtils::now();
	    	$not->viewed = 0;
	    	$not->save();
	    }
	    else{ //Se é nova, cria notificação
	    	$not = new Notification();
	    	$not->user_id = $user_id;
	    	//Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
	    	if($date) $not->date = $date;
	    	else $not->date = DateUtils::now();
	    	$not->type = self::TYPE_AUTHORIZATION;
	    	$not->viewed = 0;
	    	$not->info = (string) $auth_id;
	    	$not->save();
	    }
    }
    
    /**
     * CREATE GROUP AUTH NOTIFICATION
     * @param string $group_id
     * @param string $domain
     * @param string $reservation_id
     * @param string $auth_id
     * @return boolean
     */
    public static function createGroupAuthNotification($group_id, $domain, $reservation_id, $auth_id){
    	$group = Group::findOne($group_id);
    	 
    	$domain = Domain::findOne(['name' => $domain]);
    	 
    	if(!$group || !$domain) return false;
    	 
    	//Confere todos papeis associados ao grupo
    	$roles = UserDomainRole::findByGroup($group);
    	foreach($roles->all() as $role){
    		if($role->domain_id == null || $role->domain_id == $domain->id){ //Se papel for para todos dominios ou para dominio espeficido
    			//Confere se já foi feita uma notificação de algum circuito desta reserva, se sim, reutiliza a mesma notificação
    			$not = null;
    			$notifications = Notification::find()->where(['user_id' => $role->user_id, 'type' => self::TYPE_AUTHORIZATION])->all();
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
    				$not->date = DateUtils::now();
    				$not->viewed = 0;
    				$not->save();
    			}
    			else{ //Se for nova, cria notificação
    				$not = new Notification();
    				$not->user_id = $role->user_id;
    				$not->date = DateUtils::now();
    				$not->type = self::TYPE_AUTHORIZATION;
    				$not->viewed = 0;
    				$not->info = (string) $auth_id;
    				$not->save();
    			}
    		}
    	}
    }
    
    /**
     * CREATE CONNECTION NOTIFICATION
     * @param string $connection_id
     * Cria notificação sobre mudança no status das conexões de uma reserva
     */
    public static function createConnectionNotification($connection_id){
    	$connection = Connection::findOne($connection_id);
    	if(!$connection) return;
    	$reservation = Reservation::findOne($connection->reservation_id);
    	if(!$reservation) return;
    	
    	//Confere se já foi feita uma notificação de algum circuito desta reserva, se sim, reutiliza a mesma notificação
    	$not = null;
    	$notifications = Notification::find()->where(['user_id' => $reservation->request_user_id, 'type' => self::TYPE_RESERVATION])->all();
    	foreach($notifications as $notification){
    		if($notification->info == $reservation->id){
    			$not = $notification;
    			break;
    		}
    	}
    	
    	if($not){ //Se já existe, atualiza e coloca nova data
    		$not->date = DateUtils::now();
    		$not->viewed = 0;
    		$not->save();
    	}
    	else{ //Se for nova, cria notificação
    		$not = new Notification();
    		$not->user_id = $reservation->request_user_id;
    		$not->date = DateUtils::now();
    		$not->type = self::TYPE_RESERVATION;
    		$not->viewed = 0;
    		$not->info = (string) $reservation->id;
    		$not->save();
    	}
    }
    
    /**
     * CREATE NOTICE NOTIFICAION
     * @param string $user_id
     * @param string $type
     * @param string $data
     * @param string $data2
     * @param string $date
     * Cria notificação de alguma noticia. Esta notificações não contém link
     * Internamente ela possui um modelo particular, que armazena um JSON com tipo e e dados
     * Para noticias diferentes, criar apenas novos tipos.
     */
    public static function createNoticeNotification($user_id, $type, $data, $data2 = null, $date = null){    	 
    	$notice = [];
    	$notice[0] = $type; //Tipo da noticia
    	$notice[1] = $data; //Espaço para armaeznar info
    	if($data2) $notice[2] = $data2; //Espaço extra opcional para armazenar mais info
    	
    	$not = new Notification();
    	$not->user_id = $user_id;
    	//Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
    	if($date) $not->date = $date;
    	else $not->date = DateUtils::now();
    	$not->type = self::TYPE_NOTICE;
    	$not->viewed = 0;
    	$not->info = json_encode($notice); //Armazena todos dados em um JSON
    	$not->save();
    }
    
    /**
     * CREATE TOPOLOGY NOTIFICAION
     * @param string $msg (A tag no caso)
     * @param string $date
     * Cria notificação de mudança na topologia. VERSÃO BETA
     */
    public static function createTopologyNotification($msg, $date = null){
    	$users = User::find()->all();
    	foreach($users as $user){
    		$not = Notification::findOne(['user_id' => $user->id, 'type' => self::TYPE_TOPOLOGY, 'info' => $msg]);
    		if($not){
    			//Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
    			if($date) $not->date = $date;
    			else $not->date = DateUtils::now();
    			$not->viewed = 0;
    			$not->save();
    		}
    		else{
		    	$not = new Notification();
		    	$not->user_id = $user->id;
		    	//Pode receber uma data por parametro, neste caso, utiliza essa data como a data da criação da notificação
		    	if($date) $not->date = $date;
		    	else $not->date = DateUtils::now();
		    	$not->type = self::TYPE_TOPOLOGY;
		    	$not->viewed = 0;
		    	$not->info = $msg;
		    	$not->save();
    		}
    	}
    }
    
    /********************************
     * 
     * MAKE HTML NOTIFICATION
     *
     ********************************/
    
    /**
     * MAKE HTML NOTIFICATION NOTICE
     * @param string $notification
     * @return string
     */
    public static function makeHtmlNotificationNotice($notification = null){
    	if($notification == null) return "";
    	$data = json_decode($notification->info);
    	$type = $data[0];
    	switch($type){
    		//ADICIONADO A UM GRUPO
    		case self::NOTICE_TYPE_ADD_GROUP:
    			$group = Group::findOne($data[1]);
    			//Se não possui dado extra é para todos, do contrario, possui dominio
    			if(isset($data[2])) $domain = Domain::findOne(['name' => $data[2]]);
    			
    			$title = Yii::t("notification", 'Added to a group')." (".$group->name.")";
    			
    			$msg = Yii::t("notification", 'You have been added to group')." <b>".$group->name."</b>";
    			if(isset($domain)) $msg .= " ".Yii::t("notification", 'of the domain')." <b>".$domain->name."</b>.";
    			else if(!isset($data[2])) $msg .= " ".Yii::t("notification", 'of all domains.');
    			else $msg .= ".";
    			
    			$date = Yii::$app->formatter->asDatetime($notification->date);
    			
    			$text = '<span><h1>'.$title.'</h1><h2>'.$msg.'</h2><h3>'.$date.'</h3></span>';
    			break;
    		
    		//REMOVIDO DE UM GRUPO
    		case self::NOTICE_TYPE_DEL_GROUP:
    			$group = Group::findOne($data[1]);
    			if(isset($data[2])) $domain = Domain::findOne(['name' => $data[2]]);

    			$title = Yii::t("notification", 'Removed from a group')." (".$group->name.")";
    			 
    			$msg = Yii::t("notification", 'You were removed from the group')." <b>".$group->name."</b>";
    			if(isset($domain)) $msg .= " ".Yii::t("notification", 'of the domain')." <b>".$domain->name."</b>.";
    			else if(!isset($data[2])) $msg .= " ".Yii::t("notification", 'of all domains.');
    			else $msg .= ".";
    			 
    			$date = Yii::$app->formatter->asDatetime($notification->date);
    			 
    			$text = '<span><h1>'.$title.'</h1><h2>'.$msg.'</h2><h3>'.$date.'</h3></span>';
    			break;
    	}

    	$html = Notification::makeHtml('notice.png', $text);
    	 
    	if($notification->viewed == true) return '<li>'.$html.'</li>';
    	return '<li class="new">'.$html.'</li>';
    }
    
    /**
     * MAKE HTML NOTIFICATION RESERVATION
     * @param string $notification
     * @return string
     */
    public static function makeHtmlNotificationReservation($notification = null){
    	if($notification == null) return "";
    	$reservation = Reservation::findOne($notification->info);
    	if(!$reservation) return "";

    	$connection = Connection::find()->where(['reservation_id' => $reservation->id])->one();
    	$source = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => 0])->domain;
    	$path_order = ConnectionPath::find()->where(['conn_id' => $connection->id])->count()-1;
    	$destination = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => $path_order])->domain;
    	 
    	$title = Yii::t("notification", 'Reservation')." (".$reservation->name.")";
    	
    	$connections = Connection::find()->where(['reservation_id' => $reservation->id])->all();
    	
    	//Se possui apenas uma conexão, então pode informar diretamente se foi aceito ou negado
    	if(count($connections)<2){
    		$msg = Yii::t("notification", 'The connection between')." <b>".$source." </b>".Yii::t("notification", 'and')." <b>".$destination."</b> ";
    		$date = Yii::$app->formatter->asDatetime($notification->date);
    		$link = '/circuits/reservation/view?id='.$reservation->id;
    		
    		if($connections[0]->status == Connection::STATUS_FAILED_CREATE ||
    		   $connections[0]->status == Connection::STATUS_FAILED_CONFIRM ||
    		   $connections[0]->status == Connection::STATUS_FAILED_SUBMIT ||
    		   $connections[0]->status == Connection::STATUS_FAILED_PROVISION ||
    		   $connections[0]->auth_status == Connection::AUTH_STATUS_REJECTED ||
    		   $connections[0]->auth_status == Connection::AUTH_STATUS_EXPIRED
    		){
    			$msg .= " ".Yii::t("notification", 'can not be provisioned.');
    			$text = '<span><h1>'.$title.'</h1><h2>'.$msg.'</h2><h3>'.$date.'</h3></span>';
    			$html = Notification::makeHtml('circuit_reject.png', $text);
    		}
    		else{
    			$msg .= " ".Yii::t("notification", 'was provisioned.');
    			$text = '<span><h1>'.$title.'</h1><h2>'.$msg.'</h2><h3>'.$date.'</h3></span>';
    			$html = Notification::makeHtml('circuit_accept.png', $text);
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
    					$conn->auth_status == Connection::AUTH_STATUS_REJECTED ||
    					$conn->auth_status == Connection::AUTH_STATUS_EXPIRED
    					) $reject++;
    			else $pending++;
    		}

    		$msg = Yii::t("notification", 'The status of connections changed:')."<br />";
    		$msg .= Yii::t("notification", 'Provisioned:')." ".$provisioned.", ";
    		$msg .= Yii::t("notification", 'Rejected:')." ".$reject.", ";
    		$msg .= Yii::t("notification", 'Pending:')." ".$pending;
    		
    		$date = Yii::$app->formatter->asDatetime($notification->date);
    		$link = '/circuits/reservation/view?id='.$reservation->id;
    		
    		$text = '<span><h1>'.$title.'</h1><h2>'.$msg.'</h2><h3>'.$date.'</h3></span>';
    		$html = Notification::makeHtml('circuit_changed.png', $text);
    	}
    	
    	if($notification->viewed == true) return '<li>'.Html::a($html, array($link)).'</li>';
    	return '<li class="new">'.Html::a($html, array($link)).'</li>';
    }
    
    /**
     * MAKE HTML NOTIFICATION AUTHORIZATION
     * @param string $notification
     * @return string
     */
    public static function makeHtmlNotificationAuth($notification = null){
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
    	
    	$title = Yii::t("notification", 'Pending authorization')." (".$auth->domain.")";
	    $msg = Yii::t("notification", 'The connection is from')." <b>".$source."</b> ".Yii::t("notification", 'to')." <b>".$destination."</b>";
		$msg .= ". ".Yii::t("notification", 'The request bandwidth is')." ".$reservation->bandwidth." Mbps.";
		$date = Yii::$app->formatter->asDatetime($notification->date);
		
		$link = '/circuits/authorization/answer?id='.$reservation->id.'&domain='.$auth->domain;

	    $text = '<span><h1>'.$title.'</h1><h2>'.$msg.'</h2><h3>'.$date.'</h3></span>';

	    $html = Notification::makeHtml('pending_authorization.png', $text);
	    
	    if($notification->viewed == true) return '<li>'.Html::a($html, array($link)).'</li>';
	    return '<li class="new">'.Html::a($html, array($link)).'</li>';
    }
    
    /**
     * MAKE HTML NOTIFICATION TOPOLOGY
     * @param string $notification
     * @return string
     */
    public static function makeHtmlNotificationTopology($notification = null){
    	if($notification == null) return "";
    	$info = $notification->info;

    	$title = Yii::t("notification", 'Topology Change');
    	
    	$userId = Yii::$app->user->getId();
    	$msg = "BLA BLA BLA BLA BLA BLA BLA BLA BLA    = ".$info;
    	$date = Yii::$app->formatter->asDatetime($notification->date);
    
    	$link = '/init';
    
    	$text = '<span><h1>'.$title.'</h1><h2>'.$msg.'</h2><h3>'.$date.'</h3></span>';
    
    	$html = Notification::makeHtml('topology_changed.png', $text);
    	 
    	if($notification->viewed == true) return '<li>'.Html::a($html, array($link)).'</li>';
    	return '<li class="new">'.Html::a($html, array($link)).'</li>';
    }
    
    /********************************
     *
     * CHANGE GROUP NOTIFICATION
     *
     ********************************/
    
    /**
     * CREATE NOTIFICATIONS USER NEW GROUP
     * @param string $user_id
     * @param string $group_name
     * @param string $domain_id
     * Cria novas notificações quando usuário é adicionado a um grupo
     */
    public static function createNotificationsUserNewGroup($user_id, $group_name, $domain_id){
    	$user = User::findOne($user_id);
    	$group = Group::findOne(['role_name' => $group_name]);
    	 
    	if($user && $group){
    		Yii::trace("Criar notificações do grupo ".$group->name." para usuário ".$user->name);
    		//Busca todas autorizações pendentes do grupo
    		//Se tem dominio, procura só as relacionadas ao dominio do papel
    		if($domain_id){
    			$domain = Domain::findOne($domain_id);
    			if($domain) $auths = ConnectionAuth::find()->where(['status' => Connection::AUTH_STATUS_PENDING, 'domain' => $domain->name, 'type' => ConnectionAuth::TYPE_GROUP, 'manager_group_id' => $group->id])->all();
    			else return;
    		}
    		//Se não possui domonio no papel, busca para todos dominios, pois é ANY
    		else $auths = ConnectionAuth::find()->where(['status' => Connection::AUTH_STATUS_PENDING, 'type' => ConnectionAuth::TYPE_GROUP, 'manager_group_id' => $group->id])->all();
    
    		//Passa por todas criando uma notificação
    		foreach($auths as $auth){
    			$connection = Connection::findOne($auth->connection_id);
    			$reservation = Reservation::findOne($connection->reservation_id);
    			Notification::createUserAuthNotification($user->id, $auth->domain, $connection->reservation_id, $auth->id, $reservation->date);
    		}
    	}
    }
    
    /**
     * DELETE NOTIFICATIONS USER GROUP
     * @param string $user_id
     * @param string $group_name
     * @param string $domain_id
     * Deleta as notificações quando usuário é removido de um grupo
     */
    public static function deleteNotificationsUserGroup($user_id, $group_name, $domain_id){
    	$user = User::findOne($user_id);
    	$group = Group::findOne(['role_name' => $group_name]);
    
    	if($user && $group){
    		Yii::trace("Remover notificações do grupo ".$group->name." para usuário ".$user->name);
    		//Busca todas autorizações do grupo
    		//Se tem domínio, procura só as relacionadas ao domínio do papel
    		if($domain_id){
    			$domain = Domain::findOne($domain_id);
    			if($domain) $auths = ConnectionAuth::find()->where(['domain' => $domain->name, 'type' => ConnectionAuth::TYPE_GROUP, 'manager_group_id' => $group->id])->all();
    			else return;
    		}
    		//Se não possui domínio no papel, busca para todos dominios, pois é ANY
    		else $auths = ConnectionAuth::find()->where(['type' => ConnectionAuth::TYPE_GROUP, 'manager_group_id' => $group->id])->all();
    
    		//Passa por todas criando uma notificação
    		foreach($auths as $auth){
    			$notification = Notification::findOne(['user_id' => $user_id, 'type' => self::TYPE_AUTHORIZATION, 'info' => $auth->id]);
    			if($notification) $notification->delete();
    		}
    	}
    }

}