<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

use meican\aaa\RbacController;
use meican\base\components\DateUtils;
use meican\circuits\models\Reservation;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionAuth;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\CircuitsPreference;
use meican\circuits\models\Protocol;
use meican\circuits\models\ConnectionEvent;
use meican\circuits\models\ReservationPath;
use meican\circuits\models\CircuitNotification;
use meican\circuits\forms\ReservationForm;
use meican\circuits\forms\ReservationSearch;
use meican\topology\models\Port;
use meican\topology\models\Domain;
use meican\topology\models\Device;
use meican\topology\models\Network;
use meican\topology\models\Service;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ReservationController extends RbacController {

    public $enableCsrfValidation = false;
    
    public function actionCreate() {
        return $this->render('create/create2',['domains'=>Domain::find()->asArray()->all()]);
    }
    
    public function actionRequest() {
        $form = new ReservationForm;
        if ($form->load($_POST)) {
            
            //Confere se usuário tem permissão para reservas na origem OU no destino
            /*$source = Port::findOne(['id' => $form->src_port]);
            $destination = Port::findOne(['id' => $form->dst_port]);
            $permission = false;
            if($source){
                $source = $source->getDevice()->one();
                if($source){
                    $domain = $source->getDomain()->one();
                    if($domain && self::can('reservation/create', $domain->name)) $permission = true;
                }
            }
            if($destination){
                $destination = $destination->getDevice()->one();
                if($destination){
                    $domain = $destination->getDomain()->one();
                    if($domain &&self::can('reservation/create', $domain->name)) $permission = true;
                }
            }
            if(!$permission){ //Se ele não tiver em nenhum dos dois, exibe aviso
                return -1;
            }*/

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
    
    //Verificar, pois a cada atualizacao da pagina ele vai verificar as autorizações, 
    //isso está fora do contexto dessa função. Deveria ser feito por workflows.
    public function actionView($id) {
        $reservation = Reservation::findOne($id);
        $totalConns = $reservation->getConnections()->count();
        Yii::trace($totalConns);
        if ($totalConns == 1) {
            $this->redirect(['/circuits','id'=>$reservation->
                getConnections()->
                select(['id'])->
                asArray()->
                one()['id']]);
        }
        
        //Confere se algum pedido de autorização da expirou
        /*
        if($reservation){
            $connectionsExpired = $conn = Connection::find()->where(['reservation_id' => $reservation->id])->andWhere(['<=','start', DateUtils::now()])->all();
            foreach($connectionsExpired as $connection){
                $requests = ConnectionAuth::find()->where(['connection_id' => $connection->id, 'status' => Connection::AUTH_STATUS_PENDING])->all();
                foreach($requests as $request){
                    $request->changeStatusToExpired();
                    $connection->auth_status= Connection::AUTH_STATUS_EXPIRED;
                    $connection->save();
                    Notification::createConnectionNotification($connection->id);
                }
            }
        }

        //Confere a permissão
        $domains_name = [];
        foreach(self::whichDomainsCan('reservation/read') as $domain) $domains_name[] = $domain->name;
        $permission = false;
        if(Yii::$app->user->getId() == $reservation->request_user_id) $permission = true; //Se é quem requisitou
        else {
            $conns = Connection::find()->where(['reservation_id' => $reservation->id])->select(["id"])->all();
            if(!empty($conns)){
                $conn_ids = [];
                foreach($conns as $conn) $conn_ids[] = $conn->id;
            
                $paths = ConnectionPath::find()
                         ->where(['in', 'domain', $domains_name])
                         ->andWhere(['in', 'conn_id', $conn_ids])
                         ->select(["conn_id"])->distinct(true)->one();
                 
                if(!empty($paths)) $permission = true;
            }
        }
        
        if(!$permission){ //Se ele não tiver permissão em nenhum domínio do path e não for quem requisitou
            return $this->goHome();
        }*/
        
        $connDataProvider = new ActiveDataProvider([
                'query' => $reservation->getConnections(),
                'sort' => false,
                'pagination' => [
                    'pageSize' => 5,
                ]
        ]);

        return $this->render('view/view',[
                'reservation' => $reservation,
                'connDataProvider' => $connDataProvider
        ]);
    }

    public function actionViewGraph($id) {
        $reservation = Reservation::findOne($id);
        
        $connections = new ActiveDataProvider([
                'query' => $reservation->getConnections(),
                'sort' => false,
                'pagination' => [
                    'pageSize' => 5,
                ]
        ]);
        
        return $this->render('view/graph',[
                'reservation' => $reservation,
                'connections' => $connections,
        ]);
    }
    
    public function actionStatus() {
        $searchModel = new ReservationSearch;
        $allowedDomains = self::whichDomainsCan('reservation/read');

        $data = $searchModel->searchActiveByDomains(Yii::$app->request->get(),
                $allowedDomains);

        return $this->render('status', [
            'searchModel' => $searchModel,
            'data' => $data,
            'allowedDomains' => $allowedDomains
        ]);
    }
    
    public function actionHistory() {
        $searchModel = new ReservationSearch;
        $allowedDomains = self::whichDomainsCan('reservation/read');

        $data = $searchModel->searchTerminatedByDomains(Yii::$app->request->get(),
                $allowedDomains);

        return $this->render('history', [
            'searchModel' => $searchModel,
            'data' => $data,
            'allowedDomains' => $allowedDomains
        ]);
    }
    
    //////REST functions

    public function actionRequestUpdate($id) {
        self::asyncActionBegin();
        $res = Reservation::findOne($id);
        foreach ($res->getConnections()->all() as $conn) {
            if($conn->status != Connection::STATUS_PENDING)
                $conn->requestSummary();
        }
    }

    public function actionGetPortByDevice($id, $cols=null) {
        $query = Port::find()->where(['device_id'=>$id])->orderBy(['name'=>'SORT ASC'])->asArray();

        if (!CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_UNIPORT_ENABLED)->getBoolean()) {
            $query->andWhere(['directionality'=>Port::DIR_BI]);
        }

        if (CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_PROTOCOL)->value == Service::TYPE_NSI_CSP_2_0) {
            $query->andWhere(['type'=>Port::TYPE_NSI]);
        }

        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();

        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
}