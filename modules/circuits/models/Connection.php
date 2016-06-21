<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\models;

use Yii;

use meican\bpm\models\BpmFlow;
use meican\topology\models\Domain;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionAuth;
use meican\base\components\DateUtils;

/**
 * Represents a circuit object.
 *
 * @property integer $id
 * @property string $type
 * @property string $external_id
 * @property string $status
 * @property string $dataplane_status
 * @property string $auth_status
 * @property string $start
 * @property string $finish
 * @property integer $reservation_id
 *
 * @property BpmFlowControl[] $bpmFlowControls
 * @property Reservation $reservation
 *
 * @author Maurício Quatrin Guerreiro
 */
class Connection extends \yii\db\ActiveRecord
{
	const STATUS_PENDING = 			"PENDING";
	const STATUS_CREATED = 			"CREATED";
	const STATUS_CONFIRMED = 		"CONFIRMED";
	const STATUS_SUBMITTED = 		"SUBMITTED";
	const STATUS_PROVISIONED = 		"PROVISIONED";
	const STATUS_CANCEL_REQ = 		"CANCEL REQUESTED";
	const STATUS_CANCELLED = 		"CANCELLED";
	const STATUS_FAILED_CREATE = 	"FAILED ON CREATED";
	const STATUS_FAILED_CONFIRM = 	"FAILED ON CONFIRM";
	const STATUS_FAILED_SUBMIT = 	"FAILED ON SUBMIT";
	const STATUS_FAILED_PROVISION = "FAILED ON PROVISION";
	
	const DATA_STATUS_ACTIVE = 		"ACTIVE";
	const DATA_STATUS_INACTIVE = 	"INACTIVE";

	const AUTH_STATUS_PENDING = 	"WAITING";
	const AUTH_STATUS_APPROVED = 	"AUTHORIZED";
	const AUTH_STATUS_REJECTED = 	"DENIED";
	const AUTH_STATUS_EXPIRED = 	"EXPIRED";
	const AUTH_STATUS_UNEXECUTED = 	"UNEXECUTED";
	const AUTH_STATUS_UNSOLICITED = "UNSOLICITED";

    const TYPE_NSI = "NSI";
    const TYPE_OSCARS = "OSCARS";
 	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meican_connection';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'bandwidth', 'type','dataplane_status', 'auth_status', 'start', 'finish'], 'required'],
            [['status', 'type','dataplane_status', 'auth_status', 'name'], 'string'],
            [['start', 'finish'], 'safe'],
            [['reservation_id', 'bandwidth'], 'integer'],
            [['external_id'], 'string', 'max' => 65],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => Yii::t("circuits", 'Type'),
            'version' => Yii::t("circuits", 'Version'),
            'external_id' => Yii::t('circuits', 'Circuit ID'),
            'status' => Yii::t("circuits", 'Reservation'),
            'dataplane_status' =>  Yii::t("circuits", 'Connectivity'),
            'auth_status' =>  Yii::t("circuits", "Authorization"),
            'start' =>  Yii::t("circuits", 'Start'),
            'finish' =>  Yii::t("circuits", 'End'),
            'reservation_id' => 'Reservation ID',
            'protected' => Yii::t("circuits", 'Protection'),
        ];
    }

    public function buildEvent($eventType, $message = null) {
        $event = new ConnectionEvent;
        $event->type = $eventType;
        $event->created_at = DateUtils::now();
        $event->conn_id = $this->id;
        $event->message = $message;
        return $event;
    }

    public function getRequesterService() {
        if (Yii::$app->params['provider.force.dummy']) {
            return new \meican\circuits\services\DummyRequester($this);
        }
        return new \meican\circuits\services\NSIRequester($this);
    }

    public function getLastUserUpdateEvent() {
        return $this->getHistory()->where(['type'=>ConnectionEvent::TYPE_USER_UPDATE])->orderBy('id DESC')->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservation()
    {
        return $this->hasOne(Reservation::className(), ['id' => 'reservation_id']);
    }

    public function getStartDateTime() {
        return DateUtils::fromDB($this->start);
    }

    public function getEndDateTime() {
        return DateUtils::fromDB($this->finish);
    }

    public function getPath($order) {
        return ConnectionPath::find()->where(['conn_id'=>$this->id, 'path_order'=>$order]);
    }
    
    public function getPaths() {
        return ConnectionPath::find()->where(['conn_id'=>$this->id])->orderBy(['path_order'=> "SORT ASC"]);
    }
    
    public function getFirstPath() {
        return ConnectionPath::find()->where(['conn_id'=>$this->id,
                'path_order'=> 0]);
    }
    
    public function getLastPath() {
        return ConnectionPath::find()->where(['conn_id'=>$this->id,
                'path_order'=> ConnectionPath::find()->where(['conn_id'=>$this->id])->max('path_order')]);
    }

    public function getProvider() {
        return $this->getReservation()->select(['provider_nsa'])->getProvider();
    }

    public function getAuthorizations()
    {
        return $this->hasMany(ConnectionAuth::className(), ['id' => 'connection_id']);
    }

    public function getHistory()
    {
        return $this->hasMany(ConnectionEvent::className(), ['conn_id' => 'id']);
    }

    public function getFullPath()
    {
        return $this->hasMany(ConnectionPath::className(), ['conn_id' => 'id']);
    }

    public function getParent() {
        return $this->hasOne(Connection::className(), ['id'=> 'parent_id']);
    }

    public function getName() {
        return $this->getReservation()->select(['name'])->asArray()->one()['name'];
    }
    
    public function requestCreate() {
        $event = $this->buildEvent(ConnectionEvent::TYPE_USER_CREATE);
        $event->author_id = Yii::$app->user->getId();
        $event->setData(json_encode([
            'bandwidth'=>$this->bandwidth,
            'start'=>$this->start,
            'end'=>$this->finish]));
        $event->save();

    	$this->getRequesterService()->create();
    }

    public function requestUpdate() {
        $this->getRequesterService()->update();
    }
    
    public function requestCommit() {
        $this->getRequesterService()->commit();
    }
    
    public function requestInfo() {
        $this->getRequesterService()->info();
    }
    
    public function requestProvision() {
        $this->getRequesterService()->provision();
    }
    
    public function requestCancel() {
    	$this->status = self::STATUS_CANCEL_REQ;
    	$this->save();
    	
    	//Cancela possivel pedido de autorização pendente
    	ConnectionAuth::cancelConnAuthRequest($this->id);
    
        $this->getRequesterService()->cancel();
    }
    
    public function confirmCreate() {
    	$this->status = self::STATUS_CREATED;
    	$this->save();
    }
    
    public function confirmResources() {
    	$this->status = self::STATUS_CONFIRMED;
    	$this->save();
    	
    	//Path encontrado pelo provider, temos que submeter para obter o caminho
    	$this->requestCommit();
    }
    
	public function confirmCancel() {
		$this->status = self::STATUS_CANCELLED;
		$this->save();
		ReservationNotification::create($this->id);
	} 
	
	public function confirmCommit() {
		$this->status = self::STATUS_SUBMITTED;
		$this->save();
		
		//Depois de submetido podemos solicitar o caminho encontrado pelo provider
		$this->requestInfo();
	}
	
	//circuito confirmado e caminho disponivel. Se for uma reserva normal em submissao, solicitar autorizacao para provisionamento
	public function confirmInfo() {
		if ($this->auth_status == self::AUTH_STATUS_UNEXECUTED && $this->getReservation()->one()->type == Reservation::TYPE_NORMAL) {	
			
			$this->requestAuthorization();
		} 
	}
	
	public function confirmProvision() {
		$this->status = self::STATUS_PROVISIONED;
		$this->save();
		ReservationNotification::create($this->id);
	}
	
	public function failedCreate() {
		$this->status = self::STATUS_FAILED_CREATE;
		$this->save();
		ReservationNotification::create($this->id);
	}
	
	public function failedResources() {
		$this->status = self::STATUS_FAILED_CONFIRM;
		$this->save();
		ReservationNotification::create($this->id);
	}
	
	public function failedCommit() {
		$this->status = self::STATUS_FAILED_SUBMIT;
		$this->save();
		ReservationNotification::create($this->id);
	}
	
	public function failedProvision() {
		$this->status = self::STATUS_FAILED_PROVISION;
		$this->save();
		ReservationNotification::create($this->id);
	}
	
	public function failedCancel() {
		$this->status = self::STATUS_FAILED_CANCEL;
		$this->save();
	}
	
    public function getStatus() {
    	switch($this->status) {
    		case self::STATUS_PENDING: 			return Yii::t("circuits", "Pending");
			case self::STATUS_CREATED : 		return Yii::t("circuits", "Checking resources");
			case self::STATUS_CONFIRMED : 		return Yii::t("circuits", "Getting path info");
			case self::STATUS_SUBMITTED : 		return Yii::t("circuits", "Waiting authorization");
			case self::STATUS_PROVISIONED : 	return Yii::t("circuits", "Provisioned");
			case self::STATUS_CANCEL_REQ : 		return Yii::t("circuits", "Cancel requested");
			case self::STATUS_CANCELLED : 		return Yii::t("circuits", "Cancelled");
			case self::STATUS_FAILED_CREATE: 	return Yii::t("circuits", "Rejected by provider");
			case self::STATUS_FAILED_CONFIRM : 	return Yii::t("circuits", "Resources unavailable");
			case self::STATUS_FAILED_SUBMIT : 	return Yii::t("circuits", "Preparing failed");
			case self::STATUS_FAILED_PROVISION :return Yii::t("circuits", "Provision failed");
    	}
    }
    
    public function getDataStatus() {
    	switch($this->dataplane_status) {
    		case self::DATA_STATUS_ACTIVE: 		return Yii::t("circuits", "Active");
    		case self::DATA_STATUS_INACTIVE : 	return Yii::t("circuits", "Inactive");
    	}
    }
    
    public function getAuthStatus() {
    	switch($this->auth_status) {
    		case self::AUTH_STATUS_PENDING: 	return Yii::t("circuits", "Waiting");
    		case self::AUTH_STATUS_APPROVED : 	return Yii::t("circuits", "Approved");
    		case self::AUTH_STATUS_REJECTED : 	return Yii::t("circuits", "Rejected");
    		case self::AUTH_STATUS_EXPIRED : 	return Yii::t("circuits", "Expired");
    		case self::AUTH_STATUS_UNEXECUTED : 	return Yii::t("circuits", "Unexecuted");
    		case self::AUTH_STATUS_UNSOLICITED : 	return Yii::t("circuits", "Unsocilited");
    	}
    }
    
    public function setActiveDataStatus($bool) {
    	if ($bool) {
    		$this->dataplane_status = self::DATA_STATUS_ACTIVE;
    	} else {
    	   $this->dataplane_status = self::DATA_STATUS_INACTIVE;
        } 
        return $this;
    }
    
    public function isCancelStatus() {
    	return $this->status == self::STATUS_CANCELLED || $this->status == self::STATUS_CANCEL_REQ;
    }
    
    public function executeWorkflows($id){
    	$paths = ConnectionPath::find()->select('DISTINCT `domain`')->where(['conn_id' => $id])->all();
    	Yii::trace("Dominios envolvidos:");
    	foreach($paths as $path) Yii::trace($path->domain);
    	
    	$conn = Connection::findOne(['id' => $id]);
    	$conn->auth_status = self::AUTH_STATUS_PENDING;
    	$conn->save();
    
    	//Cria execução dos Workflows
    	foreach($paths as $path){ //Utiliza unique para não executar duas vezes em intradominios.
    		$domain = Domain::findOne(['name' => $path->domain]);
    		if(isset($domain)) BpmFlow::startFlow($id, $domain->name);
    		if(Connection::findOne(['id' => $id])->auth_status != self::AUTH_STATUS_PENDING) break; //Para quando ja negou.
    	}
    
    	//Executa primeira vez IMPEDINDO que sejam feitas perguntas.
    	//Sai caso aceite, rejeite ou entre em modo de espera para perguntar.
    	Connection::continueWorkflows($id, false);
    	 
    	//Executa segunda vez, liberando as perguntas.
    	Connection::continueWorkflows($id);
    }
    
    public static function continueWorkflows($id, $asking = true){
    	Yii::trace("CONTINUA WORKFLOWS");
    	
    	$paths = ConnectionPath::find()->select('DISTINCT `domain`')->where(['conn_id' => $id])->all();
    	Yii::trace("Dominios envolvidos:");
    	foreach($paths as $path) Yii::trace($path->domain);
    	
    	//Analisa se existem pedidos em espera. Neste momento, realiza as perguntas aos admins.
    	foreach($paths as $path){
    		if(Connection::findOne(['id' => $id])->auth_status == self::AUTH_STATUS_REJECTED) break; //Para quando ja negou.
    		else{
    			$domain = Domain::findOne(['name' => $path->domain]);
    			if(isset($domain)) BpmFlow::doRequest($id, $domain->name, $asking);
    		}
    		if(ConnectionAuth::find()->where(['connection_id' => $id, 'status' => self::AUTH_STATUS_PENDING])->count() > 0) break; //Se tem uma pergunta ativa.
    	}
    	
    	if(!$asking || ConnectionAuth::find()->where(['connection_id' => $id, 'status' => self::AUTH_STATUS_PENDING])->count() > 0) return;

    	$conn = Connection::findOne(['id' => $id]);
    	if($conn->auth_status == self::AUTH_STATUS_PENDING){
    		$conn->auth_status = self::AUTH_STATUS_APPROVED;
    		if (!$conn->save()){
    		}
    		if(!$conn->isCancelStatus()) $conn->requestProvision();
    	}
    	
    	//Remove fluxos não finalizados
    	BpmFlow::removeFlows($id);
    	
    }

    public function requestAuthorization() {
    	///// Connection aceita pelo Provider e
    	//// Path atualizado com sucesso

    	$this->executeWorkflows($this->id);
    	
    	//$this->auth_status = 'AUTHORIZED';
        //$this->save();
    	//$this->requestProvision();
    }
}