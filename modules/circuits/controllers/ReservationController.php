<?php

namespace app\modules\circuits\controllers;

use Yii;

use yii\web\Controller;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

use app\controllers\RbacController;
use app\components\AggregatorSoapClient;
use app\components\DateUtils;
use app\modules\circuits\models\AggregatorConnection;
use app\models\Reservation;
use app\models\Connection;
use app\models\ConnectionAuth;
use app\models\Port;
use app\models\Device;
use app\models\Network;
use app\modules\circuits\models\ReservationForm;
use app\modules\circuits\models\ReservationSearch;
use app\models\BpmFlow;
use app\models\ReservationPath;
use app\models\User;
use app\models\Notification;

class ReservationController extends RbacController {
	
	public $enableCsrfValidation = false;
	
    public function actionCreate() {
    	self::canRedir('reservation/create');
    	
        return $this->render('create/create');
    }
    
    public function actionRequest() {
    	$form = new ReservationForm;
    	if ($form->load($_POST)) {
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
    	self::canRedir('reservation/delete');
    	
    	$reservation = Reservation::findOne($id);
    	
    	if($reservation){
    		$connectionsExpired = $conn = Connection::find()->where(['reservation_id' => $reservation->id])->andWhere(['<=','start', DateUtils::now()])->all();
	    	foreach($connectionsExpired as $connection){
	    		$requests = ConnectionAuth::find()->where(['connection_id' => $connection->id, 'status' => 'WAITING'])->all();
	    		foreach($requests as $request){
	    			$request->status='EXPIRED';
	    			$request->save();
	    			$connection->auth_status='EXPIRED';
	    			$connection->save();
	    			Notification::createConnectionNotification($connection->id);
	    		}
	    	}
    	}
    	
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
        $dataProvider = $searchModel->searchActiveByDomains(Yii::$app->request->get(),
            $allowedDomains);

        return $this->render('status', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'allowedDomains' => $allowedDomains]);
    }
    
    public function actionHistory() {
    	$searchModel = new ReservationSearch;
        $allowedDomains = self::whichDomainsCan('reservation/read');
        $dataProvider = $searchModel->searchTerminatedByDomains(Yii::$app->request->get(),
            $allowedDomains);

        return $this->render('history', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'allowedDomains' => $allowedDomains]);
    }
    
    //////REST functions

    public function actionGetOrderedPaths($id) {
        $paths = ReservationPath::find()->where(['reservation_id'=>$id])->orderBy(['path_order'=> "SORT_ASC"])->all();
        
        $data =[];
        
        foreach ($paths as $path) {
            $urn = $path->getUrn()->one();
            $data[] = ['path_order' => $path->path_order, 'urn_id'=> $urn ? $urn->id : null];
        }
        
        $data = json_encode($data);
        Yii::trace($data);
        return $data;
    }
}