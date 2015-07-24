<?php

namespace app\modules\circuits\controllers;

use Yii;

use yii\web\Controller;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;

use app\controllers\RbacController;
use app\components\DateUtils;
use app\models\Reservation;
use app\models\Connection;
use app\models\ConnectionAuth;
use app\models\ConnectionPath;
use app\models\Port;
use app\models\Domain;
use app\models\Device;
use app\models\Network;
use app\modules\circuits\models\CircuitsPreference;
use app\modules\circuits\models\ReservationForm;
use app\modules\circuits\models\Protocol;
use app\modules\circuits\models\ReservationSearch;
use app\models\ReservationPath;
use app\models\Notification;

use yii\helpers\Json;

class ReservationController extends RbacController {
	
	public $enableCsrfValidation = false;
	
    public function actionCreate() {
    	/* Removido, pois tudo usuário passará a ter acesso ao mapa.
    	 * As permissões passam a ser conferidas no momento em que ele efetivamente solicita a reserva,
    	 * é necessário que o usuário tenho permissão de CREATE no dominio de origem ou destino.
    	 */
    	//self::canRedir('reservation/create');
    	
        return $this->render('create/create',['domains'=>Domain::find()->asArray()->all()]);
    }
    
    public function actionRequest() {
    	$form = new ReservationForm;
    	if ($form->load($_POST)) {
    		
    		//Confere se usuário tem permissão para reservas na origem OU no destino
    		$source = Port::findOne(['id' => $form->src_port]);
    		$destination = Port::findOne(['id' => $form->dst_port]);
    		$permission = false;
    		if($source){
    			$source = $source->getDevice()->one();
    			if($source){
    				$domainId = $source->domain_id;
    				if(self::can('reservation/create', $domainId)) $permission = true;
    			}
    		}
    		if($destination){
    			$destination = $destination->getDevice()->one();
    			if($destination){
    				$domainId = $destination->domain_id;
    				if(self::can('reservation/create', $domainId)) $permission = true;
    			}
    		}
    		if(!$permission){ //Se ele não tiver em nenhum dos dois, exibe aviso
    			return -1;
    		}

    		if ($form->save()) {
    			return $form->reservation->id;
    		}
    	}
    	return null;
    }
    
    public function actionConfirm() {
    	self::asyncActionBegin();
    	
    	$reservation = Reservation::findOne($_POST['id']);
    	$reservation->confirm();

		return "";
    }
    
    public function actionView($id) {
    	// Removido pois testa se é o usuário que solicitou ou se tem permissão para cancelar na origem OU destino
    	//self::can('reservation/delete');
    	
    	$reservation = Reservation::findOne($id);
    	
    	//Confere se algum pedido de autorização da expirou
    	if($reservation){
    		$connectionsExpired = $conn = Connection::find()->where(['reservation_id' => $reservation->id])->andWhere(['<=','start', DateUtils::now()])->all();
	    	foreach($connectionsExpired as $connection){
	    		$requests = ConnectionAuth::find()->where(['connection_id' => $connection->id, 'status' => Connection::AUTH_STATUS_PENDING])->all();
	    		foreach($requests as $request){
	    			$request->status= Connection::AUTH_STATUS_EXPIRED;
	    			$request->save();
	    			$connection->auth_status= Connection::AUTH_STATUS_EXPIRED;
	    			$connection->save();
	    			Notification::createConnectionNotification($connection->id);
	    		}
	    	}
    	}

        /*RESERVATION PATH PODE NAO EXISTIR
		
    	//Confere a permissão
    	$source = $reservation->getFirstPath()->one()->getPort()->one();    	
    	$destination = $reservation->getLastPath()->one()->getPort()->one();
    	$permission = false;
    	if($source){ //Se tem permissão na origem
    		$source = $source->getDevice()->one();
    		if($source){
    			$domainId = $source->domain_id;
    			if(self::can('reservation/read', $domainId)) $permission = true;
    		}
    	}
    	if($destination){ //Se tem permissão no destino
    		$destination = $destination->getDevice()->one();
    		if($destination){
    			$domainId = $destination->domain_id;
    			if(self::can('reservation/read', $domainId)) $permission = true;
    		}
    	}
    	if(Yii::$app->user->getId() == $reservation->request_user_id) $permission = true; //Se é quem requisitou
    	if(!$permission){ //Se ele não tiver em nenhum dos dois e não for quem requisitou
			return $this->goHome();
    	}
    		
            */
    	
    	$connections = new ActiveDataProvider([
    			'query' => $reservation->getConnections(),
    			'sort' => false,
    			'pagination' => [
			        'pageSize' => 5,
			    ]
    			]);
    	
    	return $this->render('view/view',[
    			'reservation' => $reservation,
    			'connections' => $connections,
    		]);
    }
    
    public function actionStatus() {
        $searchModel = new ReservationSearch;
        $allowedDomains = self::whichDomainsCan('reservation/read');
        $data_domain = $searchModel->searchActiveByDomains(Yii::$app->request->get(),
            $allowedDomains);
        
        $data_user = $searchModel->searchUserActiveByDomains(Yii::$app->request->get(),
        		Domain::find()->all());

        return $this->render('status', [
            'data_domain' => $data_domain,
            'searchModel' => $searchModel,
        	'data_user' => $data_user,
            'allowedDomains' => $allowedDomains
        ]);
    }
    
    public function actionHistory() {
    	$searchModel = new ReservationSearch;
        $allowedDomains = self::whichDomainsCan('reservation/read');
        $data_domain = $searchModel->searchTerminatedByDomains(Yii::$app->request->get(),
            $allowedDomains);

        $data_user = $searchModel->searchUserTerminatedByDomains(Yii::$app->request->get(),
        		Domain::find()->all());

        return $this->render('status', [
            'data_domain' => $data_domain,
            'searchModel' => $searchModel,
        	'data_user' => $data_user,
            'allowedDomains' => $allowedDomains]);
    }
    
    //////REST functions

    public function actionGetOrderedPaths($id) {
        $paths = ReservationPath::find()->where(['reservation_id'=>$id])->orderBy(['path_order'=> "SORT_ASC"])->all();
        
        $data =[];
        
        foreach ($paths as $path) {
            $port = $path->getPort()->select(['id','device_id'])->one();
            $data[] = ['path_order' => $path->path_order, 'device_id'=> $port ? $port->device_id : null];
        }
        
        $data = json_encode($data);
        Yii::trace($data);
        return $data;
    }

    public function actionGetPortByDevice($id, $cols=null) {
        $query = Port::find()->where(['device_id'=>$id])->asArray();

        if (!CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_UNIPORT_ENABLED)->getBoolean()) {
            $query->andWhere(['directionality'=>Port::DIR_BI]);
        }

        if (CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_PROTOCOL)->value == Protocol::TYPE_NSI_CS_2_0) {
            $query->andWhere(['type'=>Port::TYPE_NSI]);
        }

        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();

        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
}