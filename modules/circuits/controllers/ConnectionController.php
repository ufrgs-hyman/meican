<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\ConnectionEvent;
use meican\circuits\forms\ConnectionForm;
use meican\circuits\models\Reservation;
use meican\topology\models\Port;
use meican\topology\models\Domain;
use meican\topology\models\Provider;
use meican\aaa\RbacController;
use meican\oscars\services\OscarsService;

/**
 * @author Maurício Quatrin Guerreiro
 */
class ConnectionController extends RbacController {

    public $defaultAction = "view";

    public function actionView($id = null) {
        if($id == null) 
            return $this->redirect(['reservation/status']);

        if (is_numeric($id)) {
            $conn = Connection::findOne($id);
        } else {
            $conn = Connection::findOne(['external_id'=> $id]);            
        }

        if($conn === null) throw new \yii\web\HttpException(404, 'The requested item could not be found.');

        if(Yii::$app->request->isPjax)
            return $this->buildViewContent($conn);

        $history = new ActiveDataProvider([
            'query' => $conn->getHistory()->orderBy("id DESC"),
            'sort' => false,
            'pagination' => [
                'pageSize' => 6,
            ]
        ]);

        return $this->render('view/view',[
            'conn' => $conn,
            'history' => $history,
            'lastEvent' => $conn->getHistory()->orderBy("id DESC")->one()
        ]);
    }

    private function buildViewContent($conn) {
        switch ($_GET['_pjax']) {
            case '#status-pjax':
                return $this->renderAjax('view/status', [
                        'conn' => $conn, 'lastEvent' => $conn->getHistory()->orderBy("id DESC")->one()
                    ]
                );
            case '#history-pjax':
                return $this->renderAjax('view/history', [
                    'history' => new ActiveDataProvider([
                        'query' => $conn->getHistory()->orderBy("id DESC"),
                        'sort' => false,
                        'pagination' => [
                            'pageSize' => 6,
                        ]
                    ])
                ]);
            case '#details-pjax':
                return $this->renderAjax('view/details', [
                    'conn' => $conn
                ]);
        }
    }

    public function actionGetEventMessage($id) {
        if(Yii::$app->request->isAjax) {
            return $this->renderPartial('view/event-message', [
                'model' => ConnectionEvent::findOne($id)
            ]);
        }
    }

    /*public function actionGetOrderedPathsOld($id) {
        $paths = ConnectionPath::find()->where(['conn_id'=>$id])->orderBy(['path_order'=> "SORT_ASC"])->all();
         
        $data = [];
         
        foreach ($paths as $path) {
            $port = $path->getPort()->select(['id','device_id'])->one();
            $data[] = [
                'path_order' => $path->path_order, 
                'device_id'=> $port ? $port->device_id : null
            ];
        }
         
        $data = json_encode($data);
        Yii::trace($data);
        return $data;
    }*/

    public function actionGetPath($id) {
        $path = Yii::$app->db->createCommand(
            "SELECT meican_connection_path.path_order, meican_connection_path.port_urn as urn, meican_connection_path.vlan, meican_location.lat, meican_location.lng, meican_port.network_id, meican_connection_path.conn_id, meican_provider.latitude as provider_lat, meican_provider.longitude as provider_lng 
                FROM meican_connection_path
                LEFT JOIN meican_domain
                ON meican_connection_path.domain = meican_domain.name
                LEFT JOIN meican_provider
                ON meican_provider.domain_id = meican_domain.id
                LEFT JOIN meican_port 
                ON meican_connection_path.port_urn = meican_port.urn
                LEFT JOIN meican_location 
                ON meican_port.location_id = meican_location.id
                WHERE meican_connection_path.conn_id = $id"
        )->queryAll();
         
        $path = json_encode($path);
        Yii::trace($path);
        return $path;
    }

    public function actionGetEndPoints($id) {
        $conn = Connection::findOne($id);
        if ($conn->external_id) {
            $srcPath = $conn->getFirstPath()->one();
            $dstPath = $conn->getLastPath()->one();
        } else {
            $res = $conn->getReservation()->one();
            $srcPath = $res->getFirstPath()->one();
            $dstPath = $res->getLastPath()->one();
        }
        $srcPort = $srcPath->getPort()->one();
        $dstPort = $dstPath->getPort()->one();
        $srcVlan = $srcPath->vlan;
        $dstVlan = $dstPath->vlan;
        $srcPortUrn = $srcPath->getFullPortUrn();
        $dstPortUrn = $dstPath->getFullPortUrn();
        
        $source = null;
        $dest = null;

        $dev = $srcPort ? $srcPort->getDevice()->one() : null;
        $net = $srcPort ? $srcPort->getNetwork()->one() : null;
        $dom = $dev->getDomain()->one();
        
        $source["dom"] = $dom->name;
        $source["net"] = $net ? $net->name: "";
        $source["dev"] = $dev ? $dev->name: "";
        $source["port"] = $srcPort ? $srcPort->name : "";
        $source["vlan"] = $srcVlan;
        $source["urn"] = $srcPortUrn;

        $dev = $dstPort ? $dstPort->getDevice()->one() : null;
        $net = $dstPort ? $dstPort->getNetwork()->one() : null;
        $dom = $dev->getDomain()->one();
        
        $dest["dom"] = $dom->name;
        $dest["net"] = $net ? $net->name: "";
        $dest["dev"] = $dev ? $dev->name: "";
        $dest["port"] = $dstPort ? $dstPort->name : "";
        $dest["vlan"] = $dstVlan;
        $dest["urn"] = $dstPortUrn;
        
        $data = json_encode(["src" => $source, "dst" => $dest]);
        Yii::trace($data);
        return $data;
    }
    
    public function actionGetStp($id) {
        $dev = Device::findOneParentLocation($id);
        $dom = $dev->getDomain()->select(['name'])->one()->name;
        
        $data = [];
        $data['id'] = $id;
        $data["dom"] = $dom;
        $data["name"] = $dev->name;
        $data['lat'] = $dev->latitude;
        $data['lng'] = $dev->longitude;
        
        $data = json_encode($data);
        Yii::trace($data);
        return $data;
    }

    public function actionCancel($id) {
        $conn = Connection::findOne($id);
        $permission = false;
        $reservation = Reservation::findOne(['id' => $conn->reservation_id]);
        if(Yii::$app->user->getId() == $reservation->request_user_id) $permission = true; //Se é quem requisitou
        else {
            $domains_name = [];
            foreach(self::whichDomainsCan('reservation/delete') as $domain) $domains_name[] = $domain->name;
            
            $paths = ConnectionPath::find()
                     ->where(['in', 'domain', $domains_name])
                     ->andWhere(['conn_id' => $conn->id])
                     ->select(["conn_id"])->distinct(true)->one();
            
            if(!empty($paths)) $permission = true;
        }
        if($permission){
            $conn->requestCancel();
            return true;
        }
        else return false;
    }

    /**
     * Atualiza um circuito. 
     * Com submit, a função deve tentar salvar as alterações no
     * banco e criar um evento associado.
     * Com confirm, a função envia ao provedor a solicitação de
     * alteração salva a partir do submit.
     * Sem parametros, a função apenas valida se os dados fornecidos
     * podem ser alterados no circuito informado.
     *
     * @param $submit String
     * @param $confirm String opcional
     */
    public function actionUpdate($id = null, $submit = false, $confirm = false) {
        if ($confirm) {
            self::beginAsyncAction();
            $conn = Connection::findOne($id);
            //envia ao provedor uma requisicao de atualizacao do circuito
            $conn->requestUpdate();
            return "";
        }

        $form = new ConnectionForm;
        $form->load(Yii::$app->request->post());

        if (!$submit && Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($form);
        } else {
            return $form->save();
        }
    }

    public function actionRefresh($id) {
        self::beginAsyncAction();
        
        $conn = Connection::findOne($id);
        //circuitos pendentes nao possuem external ID e nao existem nos provedores
        if($conn && $conn->status != Connection::STATUS_PENDING)
            $conn->requestRead();

        return true;
    }

    //CACHED
    public function actionGetAll($status, $type) {
        self::beginAsyncAction();

        $data = Yii::$app->cache->get('circuits.oscars.all');

        if ($data === false) {
            OscarsService::loadCircuits(Yii::$app->params['oscars.bridge.provider.url']);

            // store $data in cache so that it can be retrieved next time
            Yii::$app->cache->set('circuits.oscars.all', 'true', 120000);
        } 

        $conns = Connection::find()->where(['dataplane_status'=>$status, 'type'=>$type])
                ->with('fullPath')
                ->asArray()
                ->all();

        return json_encode($conns);
    }
}

?>
