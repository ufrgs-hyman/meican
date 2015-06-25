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
use app\models\Urn;
use app\models\Device;
use app\models\Network;
use app\modules\circuits\models\ReservationForm;
use app\models\BpmFlow;
use app\models\ReservationPath;
use app\models\User;

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
    
    public function actionStatus(){
    	$res = new ActiveDataProvider([
    			'query' => Reservation::findAllActiveByDomains(self::whichDomainsCan('reservation/read')),
    			'sort' => false,
    			'pagination' => [
			        'pageSize' => 20,
			    ]
    		]);
    	
    	return $this->render('status',[
    			'data' => $res,
    		]);
    }
    
    public function actionHistory() {
    	$res = new ActiveDataProvider([
    			'query' => Reservation::findAllTerminatedByDomains(self::whichDomainsCan('reservation/read')),
    			'sort' => false,
    			'pagination' => [
			        'pageSize' => 20,
			    ]
    		]);
    	
    	return $this->render('history',[
    			'data' => $res,
    		]);
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
    
    public function actionGetStp($id) {
    	$urn = Urn::findOne($id);
        $dev = $urn->getDevice()->one();
        $net = $dev->getNetwork()->one();
        $dom = $net->getDomain()->select(['name'])->one()->name;
    	
    	$data = [];
    	$data['id'] = $id;
    	$data["dom"] = $dom;
    	$data["net"] = $net->name;
    	$data["dev"] = $dev->name;
    	$dev->latitude ? $data['lat'] = $dev->latitude : $data['lat'] = $net->latitude;
    	$dev->longitude ? $data['lng'] = $dev->longitude : $data['lng'] = $net->longitude;
    	
    	$data = json_encode($data);
    	Yii::trace($data);
    	return $data;
    }
    
    public function actionGetEndPoints($id) {
    	$res = Reservation::findOne($id);
    	$srcEndPoint = $res->getFirstPath()->one();
    	$dstEndPoint = $res->getLastPath()->one();
    	
    	$source = null;
    	$dest = null;
    	
    	$source["dom"] = $srcEndPoint->domain;
    	$source["net"] = $srcEndPoint->domain;
    	$source["dev"] = $srcEndPoint->device;
    	$source["port"] = $srcEndPoint->port;
    	$source["vlan"] = $srcEndPoint->vlan;
    	$source["urn"] = $srcEndPoint->getUrnValue();
    	
    	$dest["dom"] = $dstEndPoint->domain;
    	$dest["net"] = $dstEndPoint->domain;
    	$dest["dev"] = $dstEndPoint->device;
    	$dest["port"] = $dstEndPoint->port;
    	$dest["vlan"] = $dstEndPoint->vlan;
    	$dest["urn"] = $dstEndPoint->getUrnValue();
    	
    	$data = json_encode(["src" => $source, "dst" => $dest]);
    	Yii::trace($data);
    	return $data;
    }
}