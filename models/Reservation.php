<?php

namespace app\models;

use Yii;
use app\components\DateUtils;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%reservation}}".
 *
 * @property integer $id
 * 
 * Indica o tipo da reserva, TEST ou NORMAL.
 * Se TEST, será gerida pelo Meican, sendo recriada
 * com a frequencia indicada pelo AutomatedTest associado.
 * Reservas desse tipo não são efetivamente provisionadas.
 * Dessa forma não há alocamento de recursos.
 * Se NORMAL, a reserva será efetiva, dessa forma serão
 * executados os Workflows de Autorização de todos os 
 * Domains que o circuito passar.
 *  
 * @property string $type
 * @property string $name
 * @property integer $bandwidth
 * @property string $start
 * @property string $finish
 * 
 * Requester NSA ID que enviou a solicitação
 * 
 * @property string $requester_nsa
 *
 * Provider NSA ID que recebeu a solicitação
 * 
 * @property string $provider_nsa
 *
 * @property integer $request_user_id
 * 
 * @property AutomatedTest $automatedTest
 * @property Connection[] $connections
 * @property Provider $provider
 * @property User $requesterUser
 * @property ReservationRecurrence $reservationRecurrence
 */
class Reservation extends \yii\db\ActiveRecord
{
	const TYPE_NORMAL 	= "NORMAL";
	const TYPE_TEST 	= "TEST";
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reservation}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'name', 'date', 'bandwidth', 'start', 'finish', 'provider_nsa','requester_nsa'], 'required'],
            [['type'], 'string'],
            [['bandwidth', 'request_user_id'], 'integer'],
            [['start', 'finish', 'date'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['provider_nsa', 'requester_nsa'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => Yii::t('circuits', 'Type'),
            'name' => Yii::t('circuits', 'Name'),
            'date' => Yii::t('circuits', 'Request Date'),
            'bandwidth' => Yii::t('circuits', 'Bandwidth (Mbps)'),
            'start' => Yii::t('circuits', 'Start'),
            'finish' => Yii::t('circuits', 'Finish'),
            'request_user_id' => Yii::t('circuits', 'Requested by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBpmFlowControls()
    {
        return $this->hasMany(BpmFlowControl::className(), ['reservation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnections()
    {
        return $this->hasMany(Connection::className(), ['reservation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvider()
    {
        return $this->hasOne(Provider::className(), ['nsa' => 'provider_nsa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequesterUser()
    {
        return $this->hasOne(User::className(), ['id' => 'request_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecurrence()
    {
        return $this->hasOne(ReservationRecurrence::className(), ['id' => 'id']);
    }
    
    public function getPath($order) {
    	return ReservationPath::find()->where(['reservation_id'=>$this->id, 'path_order'=>$order]);
    }
    
    public function getPaths() {
    	return ReservationPath::find()->where(['reservation_id'=>$this->id])->orderBy(['path_order'=> "SORT ASC"]);
    }
    
    public function getFirstPath() {
    	return ReservationPath::find()->where(['reservation_id'=>$this->id,
    			'path_order'=> 0]);
    }
    
    public function getLastPath() {
    	return ReservationPath::find()->where(['reservation_id'=>$this->id,
    			'path_order'=> ReservationPath::find()->where(['reservation_id'=>$this->id])->max('path_order')]);
    }
    
    public function getDestinationUrn() {
    	$path = $this->getLastPath()->one();
    	return $path ? $path->getUrn() : null;
    }
    
    public function getSourceUrn() {
    	$path = $this->getFirstPath()->one();
    	return $path ? $path->getUrn() : null;
    }
    
    public function getSourceDomain() {
    	$path = $this->getFirstPath()->one();
    	if(!$path) return null;
    	$port = $path->getPort()->one();
    	if(!$port) return null;
    	$device = $port->getDevice()->one();
    	if(!$device) return null;
    	return $device->getDomain()->one()->name;
    }
    
    public function getDestinationDomain() {
    	$path = $this->getLastPath()->one();
    	if(!$path) return null;
    	$port = $path->getPort()->one();
    	if(!$port) return null;
    	$device = $port->getDevice()->one();
    	if(!$device) return null;
    	return $device->getDomain()->one()->name;
    }
    
    public function createConnections() {
    	$events = $this->getAllEvents();
    	$paths = $this->getPaths()->all();
    	Yii::trace($events);
    	foreach ($events as $event) {
    		$conn = new Connection;
    		$date = new \DateTime;
    
    		$date->setTimestamp($event->start);
    		$conn->start = $date->format('Y-m-d H:i');
    
    		$date->setTimestamp($event->finish);
    		$conn->finish = $date->format('Y-m-d H:i');
    
    		$conn->reservation_id = $this->id;
    		$conn->status = Connection::STATUS_PENDING;
    		$conn->dataplane_status = Connection::DATA_STATUS_INACTIVE;
    		$conn->auth_status = Connection::AUTH_STATUS_UNEXECUTED;

            if($conn->save()) {
                $i = 0;
                foreach ($paths as $resPath) {
                    $connPath = new ConnectionPath;
                    $connPath->path_order = $i;
                    $connPath->conn_id = $conn->id;
                    $connPath->domain = explode(":",$resPath->port_urn)[0];
                    $i++;
                    $connPath->port_urn = $resPath->port_urn;
                    $connPath->vlan = $resPath->vlan;
                    
                    $connPath->save();
                }
            }
    	}
    }
    
    public function confirm() {
    	foreach ($this->getConnections()->all() as $conn) {
    		if ($conn->external_id == null) {
    			$conn->requestCreate();
    		}
    	}
    }
    
    public function getAllEvents() {
    	$rec = $this->getRecurrence()->one();
    	if ($rec) {
    		return $rec->getEvents($this->start, $this->finish);
    	} else {
    		$per = new \stdClass();
    		$eventStart = new \DateTime($this->start);
    		$eventFinish = new \DateTime($this->finish);
    		$per->start = $eventStart->getTimestamp();
    		$per->finish = $eventFinish->getTimestamp();
    		$periods[] = $per;
    		return $periods;
    	}
    }
}
