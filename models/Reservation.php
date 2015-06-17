<?php

namespace app\models;

use Yii;
use app\modules\circuits\models\AggregatorConnection;
use app\components\DateUtils;

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
 * Por definição, o provedor indica qual o 
 * Connection Service será utilizado para
 * gerir essa reserva.
 * A tabela provider, pode armazenar Aggregators e
 * Bridges, sendo que um Dominio possui uma e somente
 * uma Bridge.
 * Não confundir esse Provider com o Domain associado a
 * cada ReservationPath. Esse Provider é o provedor usado
 * na requisição. O Domain de um Path representa a Bridge
 * que gerencia aquele circuito parcial.
 * 
 * @property integer $provider_id
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
            [['type', 'name', 'date', 'bandwidth', 'start', 'finish', 'provider_id', 'request_user_id'], 'required'],
            [['type'], 'string'],
            [['bandwidth', 'provider_id', 'request_user_id'], 'integer'],
            [['start', 'finish', 'date'], 'safe'],
            [['name'], 'string', 'max' => 50]
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
            'provider_id' => Yii::t('circuits', 'Provider'),
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
        return $this->hasOne(Provider::className(), ['id' => 'provider_id']);
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
    	return $path->domain;
    }
    
    public function getDestinationDomain() {
    	$path = $this->getLastPath()->one();
    	return $path->domain;
    }
    
    public function createConnections() {
    	$events = $this->getEvents();
    	$paths = $this->getPaths();
    	Yii::trace($events);
    	foreach ($events as $event) {
    		$conn = new Connection;
    		$date = new \DateTime;
    
    		$date->setTimestamp($event->start);
    		$conn->start = $date->format('Y-m-d H:i');
    
    		$date->setTimestamp($event->finish);
    		$conn->finish = $date->format('Y-m-d H:i');
    
    		$conn->reservation_id = $this->id;
    		$conn->status = "PENDING";
    		$conn->dataplane_status = "INACTIVE";
    		$conn->auth_status = 'WAITING';
    		$conn->save();
    	}
    }
    
    public function cancelConnections() {
    }
    
    public function confirm() {
    	foreach ($this->getConnections()->all() as $conn) {
    		if ($conn->external_id == null) {
    			$conn->requestCreate();
    		}
    	}
    }
    
    public function getEvents() {
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

    static function findAllActiveByDomains($domains) {
        $validDomains = [];
        foreach ($domains as $domain) {
            $validDomains[] = $domain->topology;
        }

        $connPaths = ConnectionPath::find()->where(['in', 'domain', $validDomains])->select(["conn_id"])->distinct(true)->all();

        $validConnPaths = [];
        foreach ($connPaths as $connPath) {
            $validConnPaths[] = $connPath->conn_id;
        }

        $validConnections = Connection::find()->where(['>=','finish', DateUtils::now()])->andWhere(['in', 'id', $validConnPaths])->andWhere(['status'=>[
                "PENDING","CREATED","CONFIRMED","SUBMITTED","PROVISIONED"]])->select(["reservation_id"])->distinct(true)->all();

        $validIds = [];
        foreach ($validConnections as $conn) {
            $validIds[] = $conn->reservation_id;
        }
        Yii::trace($validIds);

        return self::find()->where(['in', 'id', $validIds])->andWhere(
                        ['type'=>self::TYPE_NORMAL])->orderBy(['date'=>SORT_DESC]);
    }

    static function findAllTerminatedByDomains($domains) {
        $validDomains = [];
        foreach ($domains as $domain) {
            $validDomains[] = $domain->topology;
        }

        Yii::trace($validDomains);

        $connPaths = ConnectionPath::find()->where(['in', 'domain', $validDomains])->select(["conn_id"])->distinct(true)->all();

        $validConnPaths = [];
        foreach ($connPaths as $connPath) {
            $validConnPaths[] = $connPath->conn_id;
        }

        $validConns = Connection::find()->where(['in','id',$validConnPaths])->select('reservation_id')->distinct(true)->all();

        $validIds = [];
        foreach ($validConns as $conn) {
           $validIds[] = $conn->reservation_id;
        }

        $invalidConnections = Connection::find()->where(['>=','finish', DateUtils::now()])->andWhere(['status'=>[
                "PENDING","CREATED","CONFIRMED","SUBMITTED","PROVISIONED"]])->select(["reservation_id"])->distinct(true)->all();

        $invalidIds = [];
        foreach ($invalidConnections as $conn) {
           $invalidIds[] = $conn->reservation_id;
        }

        return self::find()->where(['not in', 'id', $invalidIds])->andWhere(['in', 'id', $validIds])->andWhere(
                        ['type'=>self::TYPE_NORMAL])->orderBy(['date'=>SORT_DESC]);
    }
}
