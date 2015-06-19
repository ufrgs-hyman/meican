<?php

namespace app\modules\circuits\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\models\Reservation;
use app\models\ReservationPath;
use app\models\Connection;
use app\models\ConnectionAuth;
use app\models\ConnectionPath;
use app\models\BpmFlow;
use app\models\FlowPath;
use app\models\User;
use app\models\Domain;
use yii\db\Query;
use app\modules\circuits\models\AuthorizationForm;

use app\components\DateUtils;

class AuthorizationController extends RbacController {
	
	public $enableCsrfValidation = false;

    public function actionIndex(){
    	Yii::trace("Authorization");
    	$userId = Yii::$app->user->getId();
    	
    	$now = DateUtils::now();
    	
    	$authorizations = []; //Armazena os pedidos
    	$reservationsVisited = []; //Armazena as reservas ja incluidas nos pedidos e o dominio ao qual o pedido foi feito.

    	//Pega todas requisições feitas para o usuário
    	$userRequests = ConnectionAuth::find()->where(['manager_user_id' => $userId, 'status' => 'WAITING'])->all();
    	foreach($userRequests as $request){ //Limpa mantendo apenas 1 por reserva
    		$uniq = true;
    		$conn = Connection::find()->where(['id' => $request->connection_id])->andWhere(['<=','start', DateUtils::now()])->one();
    		if(isset($conn)){
    			$request->status='EXPIRED';
    			$request->save();
    			$conn->auth_status='EXPIRED';
    			$conn->save();
    		}
    		else{
    			$conn = Connection::find()->where(['id' => $request->connection_id])->andWhere(['>','start', DateUtils::now()])->one();
	    		foreach($reservationsVisited as $res){
	    			if($conn->reservation_id == $res[0] && $request->domain == $res[1]){
	    				$uniq = false;
	    			}
	    		}
	    		if($uniq){
	    			$aux = [];
	    			$aux[0] = $conn->reservation_id;;
	    			$aux[1] = $request->domain;
	    			$reservationsVisited[] = $aux;
	    			$domain = Domain::findOne(['topology' => $request->domain]);
	    			if(isset($domain))
    					$authorizations[] = new AuthorizationForm(Reservation::findOne(['id' => $conn->reservation_id]), $domain);
	    		}
    		}
    	}
    	
    	//Pega todos os papeis do usuário
    	$domainRoles = User::findOne(['id' => $userId])->getUserDomainRoles()->all();
    	foreach($domainRoles as $role){ //Passa por todos papeis
    		$groupRequests = ConnectionAuth::find()->where(['manager_group_id' => $role->getGroup()->id, 'status' => 'WAITING'])->all();
    		
    		foreach($groupRequests as $request){ //Passa por todos para testar se o dominio corresponde
    			$domain = Domain::findOne(['topology' => $request->domain]);
    			if($domain){
    				//Reescrita necessária quando utilizar a tabela de notificações. Pode abstrair muita coisa e ler de la.
	    			if($role->domain_id == NULL || $role->domain_id == $domain->id){
		    			$uniq = true;
		    			$conn = Connection::find()->where(['id' => $request->connection_id])->andWhere(['<=','start', DateUtils::now()])->one();
			    		if(isset($conn)){
			    			$request->status='EXPIRED';
			    			$request->save();
			    			$conn->auth_status='EXPIRED';
			    			$conn->save();
			    		}
		    			else{
		    				$conn = Connection::find()->where(['id' => $request->connection_id])->andWhere(['>','start', DateUtils::now()])->one();
		    				foreach($reservationsVisited as $res){
				    			if($conn->reservation_id == $res[0] && $domain->id == $res[1]){
				    				$uniq = false;
				    			}
				    		}
				    		if($uniq){
				    			$aux = [];
				    			$aux[0] = $conn->reservation_id;;
				    			$aux[1] = $request->domain->topology;
				    			$reservationsVisited[] = $aux;
				    			if(isset($domain))
			    					$authorizations[] = new AuthorizationForm(Reservation::findOne(['id' => $conn->reservation_id]), $domain);
			    			}
		    			}
		    		}
    			}
    		}
    	}

    	$dataProvider = new ArrayDataProvider([
    			'allModels' => $authorizations,
    			'sort' => false,
    			'pagination' => false,
    	]);
    
    	return $this->render('index', array(
    			'data' => $dataProvider,
    	));
    
    }
    
    public function actionAnswer($id = null, $domain = null){
    	Yii::trace("Answer");
    	if($id == null || $domain == null) $this->actionAuthorization();
    	else{
    		if(!Domain::findOne(['topology' => $domain])) $this->actionAuthorization();
    		else{
	    		Yii::trace("Respondendo a reserva id: ".$id);
	    		$userId = Yii::$app->user->getId();
	    
	    		$reservation = Reservation::findOne(['id' => $id]);
	    
	    		$allRequest = null;
	    		$connections = Connection::find()->where(['reservation_id' => $id])->all();
	    		foreach($connections as $conn){
	    			if($allRequest == null) $allRequest = ConnectionAuth::find()->where(['connection_id' => $conn->id, 'domain' => $domain]);
	    			else $allRequest->union( ConnectionAuth::find()->where(['connection_id' => $conn->id, 'domain' => $domain]));
	    		}
	
	    		$allRequest = $allRequest->all();
	    		$domainRules = User::findOne(['id' => $userId])->getUserDomainRoles()->all();
	    		$requests = [];
	    		foreach($allRequest as $request){
	    			if($request->manager_user_id == $userId) $requests[$request->id] = $request;
	    			else{
	    				foreach($domainRules as $domainRule){
	    					$groupId = $domainRule->getGroup()->id;
	    					if($request->manager_group_id == $groupId) $requests[$request->id] = $request;
	    				}
	    			}
	    		}
	    		$events = [];
	    		foreach($requests as $request){
	    			$events[] = ['id' => $request->id, 'title' => "\n".$request->getConnection()->one()->getReservation()->one()->bandwidth." Mbps", 'start' => Yii::$app->formatter->asDatetime( $request->getConnection()->one()->start, "php:Y-m-d H:i:s"), 'end' => Yii::$app->formatter->asDatetime($request->getConnection()->one()->finish, "php:Y-m-d H:i:s")];
	    		}
	
	    		if(sizeof($requests)<=0) return $this->redirect('index');
		    	else return $this->render('detailed', array(
	    				'domain' => $domain,
	    				'info' => $reservation,
	    				'requests' => $requests,
	    				'events' => $events
	    		));
    		}
    	}
    }
    
    public function actionGetOthers($domainTop = null, $reservationId = null, $type = null){
    	if($domainTop && $reservationId && $type){
	    	$connectionsPath = ConnectionPath::find()->select('DISTINCT `conn_id`')->where(['domain' => $domainTop])->all();
	    	$others = [];
    		foreach($connectionsPath as $path){
    			$con = Connection::findOne(['id' => $path->conn_id]);
    			//Tipo 1 significa que esta retornando os provisionados
    			if($type == 1 && $con->status == "PROVISIONED"){
    				//Se é a mesma reserva, testa para garantir que é de outro dominio antes de exibir
    				if($con->reservation_id != $reservationId) $others[] = ['id' => $con->id, 'title' => "\n".$con->getReservation()->one()->bandwidth." Mbps", 'start' => Yii::$app->formatter->asDatetime($con->start, "php:Y-m-d H:i:s"), 'end' => Yii::$app->formatter->asDatetime($con->finish, "php:Y-m-d H:i:s")];			
    			}
    			//Tipo 2 signigica que esta retornando aqueles que ainda estão sendo processados
    			else if($type == 2 && $con->status != "PROVISIONED" && $con->auth_status != "DENIED" && $con->auth_status != "EXPIRED"){
    				//Se é a mesma reserva, testa para garantir que é de outro dominio antes de exibir
    				Yii::error($con->reservation_id." - ".$reservationId);
    				if($con->reservation_id == $reservationId){
    					if(ConnectionAuth::findOne(['connection_id' => $con->id, 'status' => 'WAITING'])->domain != $domainTop)
    						$others[] = ['id' => $con->id, 'title' => "\n".$con->getReservation()->one()->bandwidth." Mbps", 'start' => Yii::$app->formatter->asDatetime($con->start, "php:Y-m-d H:i:s"), 'end' => Yii::$app->formatter->asDatetime($con->finish, "php:Y-m-d H:i:s")];
    				}
    				else $others[] = ['id' => $con->id, 'title' => "\n".$con->getReservation()->one()->bandwidth." Mbps", 'start' => Yii::$app->formatter->asDatetime($con->start, "php:Y-m-d H:i:s"), 'end' => Yii::$app->formatter->asDatetime($con->finish, "php:Y-m-d H:i:s")];
    			}
    		}
	    	echo json_encode($others);
    	}

    }
    
    public function actionIsAnswered($id = null){
    	if($id){
    		$status = ConnectionAuth::findOne(['id' => $id])->status;
    		Yii::trace($status);
    		if($status=="WAITING") echo 0;
    		else echo 1;
    	}
    }
    
    public function actionAcceptAll($id = null, $domainTop = null, $message = null){
    	Yii::trace("Accept ALL");
    	Yii::trace("ID: ".$id);
    	Yii::trace("Msg: ".$message);
    	if($id && $domainTop){
    		$userId = Yii::$app->user->getId();
    		$reservation = Reservation::findOne(['id' => $id]);
    		
    		$allRequest = null;
    		$connections = Connection::find()->where(['reservation_id' => $id])->all();
    		foreach($connections as $conn){
    			if($allRequest == null) $allRequest = ConnectionAuth::find()->where(['connection_id' => $conn->id, 'domain' => $domainTop]);
    			else $allRequest->union( ConnectionAuth::find()->where(['connection_id' => $conn->id, 'domain' => $domainTop]));
    		}
    		
    		$allRequest = $allRequest->all();
    		$domainRules = User::findOne(['id' => $userId])->getUserDomainRoles()->all();
    		$requests = [];
    		foreach($allRequest as $request){
    			if($request->manager_user_id == $userId) $requests[$request->id] = $request;
    			else{
    				foreach($domainRules as $domainRule){
    					$groupId = $domainRule->getGroup()->id;
    					if($request->manager_group_id == $groupId) $requests[$request->id] = $request;
    				}
    			}
    		}
    		
    		foreach($requests as $req){
    			if($req->status != "AUTHORIZED" && $req->status != "DENIED"){
    				if($req->type == "GROUP") $req->manager_user_id = Yii::$app->user->getId();
    				if($message) $req->manager_message = $message;
    				$req->status = 'AUTHORIZED';
    				$req->save();
    		
    				$flow = new BpmFlow;
    				$flow->response($req->connection_id, $req->domain, "YES");
    			}
    		}
    	}
    }
    
    public function actionRejectAll($id = null, $domainTop = null, $message = null){
    	Yii::trace("Reject ALL");
    	Yii::trace("Reservation ID: ".$id);
    	Yii::trace("Msg: ".$message);
    	if($id && $domainTop){
    		$userId = Yii::$app->user->getId();
    		$reservation = Reservation::findOne(['id' => $id]);
    
    		$allRequest = null;
    		$connections = Connection::find()->where(['reservation_id' => $id])->all();
    		foreach($connections as $conn){
    			if($allRequest == null) $allRequest = ConnectionAuth::find()->where(['connection_id' => $conn->id, 'domain' => $domainTop]);
    			else $allRequest->union( ConnectionAuth::find()->where(['connection_id' => $conn->id, 'domain' => $domainTop]));
    		}
    
    		$allRequest = $allRequest->all();
    		$domainRules = User::findOne(['id' => $userId])->getUserDomainRoles()->all();
    		$requests = [];
    		foreach($allRequest as $request){
    			if($request->manager_user_id == $userId) $requests[$request->id] = $request;
    			else{
    				foreach($domainRules as $domainRule){
    					$groupId = $domainRule->getGroup()->id;
    					if($request->manager_group_id == $groupId) $requests[$request->id] = $request;
    				}
    			}
    		}
    		
    		foreach($requests as $req){
    			if($req->status != "AUTHORIZED" && $req->status != "DENIED"){
    				if($req->type == "GROUP") $req->manager_user_id = Yii::$app->user->getId();
    				if($message) $req->manager_message = $message;
    				$req->status = 'DENIED';
    				$req->save();
    				
    				$flow = new BpmFlow;
    				$flow->response($req->connection_id, $req->domain, "NO");
    			}
    		}
    	}
    }
    
    public function actionAccept($id = null, $message = null){
    	Yii::trace("Accept");
    	Yii::trace("ID: ".$id);
    	Yii::trace("Msg: ".$message);
    	if($id){
    		$req = ConnectionAuth::findOne(['id' => $id]);
    		if($req->type == "GROUP") $req->manager_user_id = Yii::$app->user->getId();
    		if($message) $req->manager_message = $message;
    		$req->status = 'AUTHORIZED';
    		$req->save();
    		
    		$flow = new BpmFlow;
    		$flow->response($req->connection_id, $req->domain, "YES");	
    	}
    }
    
    public function actionReject($id = null, $message = null){
    	Yii::trace("Reject");
    	Yii::trace("ID: ".$id);
    	Yii::trace("Msg: ".$message);
    	if($id){
    		$req = ConnectionAuth::findOne(['id' => $id]);
    		if($req->type == "GROUP") $req->manager_user_id = Yii::$app->user->getId();
    		if($message != null) $req->manager_message = $message;
    		$req->status = 'DENIED';
    		$req->save();
    		
    		$flow = new BpmFlow;
    		$flow->response($req->connection_id, $req->domain, "NO");
    	}
    }
    
    public function actionGetNumberAuths(){
    	echo ConnectionAuth::getNumberAuth();
    }

}
