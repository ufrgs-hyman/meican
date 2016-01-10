<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;

use meican\aaa\RbacController;
use meican\aaa\models\User;
use meican\circuits\models\Reservation;
use meican\circuits\models\ReservationPath;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionAuth;
use meican\circuits\models\ConnectionPath;
use meican\circuits\forms\AuthorizationForm;
use meican\circuits\forms\AuthorizationDetailed;
use meican\circuits\forms\AuthorizationSearch;
use meican\bpm\models\BpmFlow;
use meican\topology\models\Domain;
use meican\notification\models\Notification;
use meican\base\components\DateUtils;

class AuthorizationController extends RbacController {
    
    public $enableCsrfValidation = false;

    public function actionIndex(){
        Yii::trace("Authorization");
        
        $searchModel = new AuthorizationSearch;
        $data = $searchModel->searchByDomains(Yii::$app->request->get());
    
        return $this->render('index', array(
                'searchModel' => $searchModel,
                'data' => $data,
        ));
    
    }
    
    public function actionAnswer($id = null, $domain = null){
        Yii::trace("Answer");
        if($id == null || $domain == null) $this->actionAuthorization();
        else{
            if(!Domain::findOne(['name' => $domain])) $this->actionAuthorization();
            else{
                Yii::trace("Respondendo a reserva id: ".$id);
                $userId = Yii::$app->user->getId();
        
                $reservation = Reservation::findOne(['id' => $id]);
                
                //Confere se alguma ja expirou
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

                $allRequest = null;
                $connections = Connection::find()->where(['reservation_id' => $id])->all();
                foreach($connections as $conn){
                    if($allRequest == null) $allRequest = ConnectionAuth::find()->where(['connection_id' => $conn->id, 'domain' => $domain]);
                    else $allRequest->union( ConnectionAuth::find()->where(['connection_id' => $conn->id, 'domain' => $domain]));
                }
    
                $allRequest = $allRequest->all();
                $domainRoles = User::findOne(['id' => $userId])->getUserDomainRoles()->all();
                $requests = [];
                foreach($allRequest as $request){
                    if($request->manager_user_id == $userId) $requests[$request->id] = $request;
                    else{
                        foreach($domainRoles as $domainRule){
                            $groupId = $domainRule->getGroup()->id;
                            if($request->manager_group_id == $groupId) $requests[$request->id] = $request;
                        }
                    }
                }
                $events = [];
                foreach($requests as $request){
                    $events[] = ['id' => $request->id, 'title' => "\n".$request->getConnection()->one()->getReservation()->one()->bandwidth." Mbps", 'start' => Yii::$app->formatter->asDatetime( $request->getConnection()->one()->start, "php:Y-m-d H:i:s"), 'end' => Yii::$app->formatter->asDatetime($request->getConnection()->one()->finish, "php:Y-m-d H:i:s")];
                }
                
                $info = new AuthorizationDetailed($reservation, Connection::find()->where(['reservation_id' => $id])->one()->id, $domain);
    
                if(sizeof($requests)<=0) return $this->redirect('index');
                else return $this->render('detailed', array(
                        'domain' => $domain,
                        'info' => $info,
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
                if($type == 1 && $con->status == Connection::STATUS_PROVISIONED){
                    //Se é a mesma reserva, testa para garantir que é de outro dominio antes de exibir
                    if($con->reservation_id != $reservationId) $others[] = ['id' => $con->id, 'title' => "\n".$con->getReservation()->one()->bandwidth." Mbps", 'start' => Yii::$app->formatter->asDatetime($con->start, "php:Y-m-d H:i:s"), 'end' => Yii::$app->formatter->asDatetime($con->finish, "php:Y-m-d H:i:s")];            
                }
                //Tipo 2 signigica que esta retornando aqueles que ainda estão sendo processados
                else if($type == 2 && $con->status != Connection::STATUS_PROVISIONED && $con->auth_status == Connection::AUTH_STATUS_PENDING){
                    //Se é a mesma reserva, testa para garantir que é de outro dominio antes de exibir
                    if($con->reservation_id == $reservationId){
                        if(ConnectionAuth::findOne(['connection_id' => $con->id, 'status' => Connection::AUTH_STATUS_PENDING])->domain != $domainTop)
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
            if($status == Connection::AUTH_STATUS_PENDING) echo 0;
            else echo 1;
        }
    }
    
    /**
     * ACTION ACCEPT ALL
     * @param string $id
     * @param string $domainTop
     * @param string $message
     */
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
            $domainRoles = User::findOne(['id' => $userId])->getUserDomainRoles()->all();
            $requests = [];
            foreach($allRequest as $request){
                if($request->manager_user_id == $userId) $requests[$request->id] = $request;
                else{
                    foreach($domainRoles as $domainRule){
                        $groupId = $domainRule->getGroup()->id;
                        if($request->manager_group_id == $groupId) $requests[$request->id] = $request;
                    }
                }
            }
            
            foreach($requests as $req){
                if($req->status == Connection::AUTH_STATUS_PENDING){
                    if($req->type == ConnectionAuth::TYPE_GROUP) $req->manager_user_id = Yii::$app->user->getId();
                    if($message) $req->manager_message = $message;
                    $req->status = Connection::AUTH_STATUS_APPROVED;
                    $req->save();
            
                    $flow = new BpmFlow;
                    $flow->response($req->connection_id, $req->domain, BpmFlow::STATUS_YES);
                }
            }
        }
    }
    
    /**
     * ACTION REJECT ALL
     * @param string $id
     * @param string $domainTop
     * @param string $message
     */
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
            $domainRoles = User::findOne(['id' => $userId])->getUserDomainRoles()->all();
            $requests = [];
            foreach($allRequest as $request){
                if($request->manager_user_id == $userId) $requests[$request->id] = $request;
                else{
                    foreach($domainRoles as $domainRule){
                        $groupId = $domainRule->getGroup()->id;
                        if($request->manager_group_id == $groupId) $requests[$request->id] = $request;
                    }
                }
            }
            
            foreach($requests as $req){
                if($req->status == Connection::AUTH_STATUS_PENDING){
                    if($req->type == ConnectionAuth::TYPE_GROUP) $req->manager_user_id = Yii::$app->user->getId();
                    if($message) $req->manager_message = $message;
                    $req->status = Connection::AUTH_STATUS_REJECTED;
                    $req->save();
                
                    $flow = new BpmFlow;
                    $flow->response($req->connection_id, $req->domain, BpmFlow::STATUS_NO);
                }
            }
        }
    }
    
    /**
     * ACTION ACCEPT
     * @param string $id
     * @param string $message
     */
    public function actionAccept($id = null, $message = null){
        Yii::trace("Accept");
        Yii::trace("ID: ".$id);
        Yii::trace("Msg: ".$message);
        if($id){
            $req = ConnectionAuth::findOne(['id' => $id]);
            if($req->type == ConnectionAuth::TYPE_GROUP) $req->manager_user_id = Yii::$app->user->getId();
            if($message) $req->manager_message = $message;
            $req->status = Connection::AUTH_STATUS_APPROVED;
            $req->save();
            
            $flow = new BpmFlow;
            $flow->response($req->connection_id, $req->domain, BpmFlow::STATUS_YES);    
        }
    }
    
    /**
     * ACTION REJECT
     * @param string $id
     * @param string $message
     */
    public function actionReject($id = null, $message = null){
        Yii::trace("Reject");
        Yii::trace("ID: ".$id);
        Yii::trace("Msg: ".$message);
        if($id){
            $req = ConnectionAuth::findOne(['id' => $id]);
            if($req->type == ConnectionAuth::TYPE_GROUP) $req->manager_user_id = Yii::$app->user->getId();
            if($message != null) $req->manager_message = $message;
            $req->status = Connection::AUTH_STATUS_REJECTED;
            $req->save();
            
            $flow = new BpmFlow;
            $flow->response($req->connection_id, $req->domain, BpmFlow::STATUS_NO);
        }
    }

}
