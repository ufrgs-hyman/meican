<?php

namespace app\models;

use Yii;
use app\models\ConnectionAuth;
use app\models\ConnectionPath;

/**
 * This is the model class for table "meican_connection".
 *
 * @property integer $id
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
            [['status', 'dataplane_status', 'auth_status', 'start', 'finish', 'reservation_id'], 'required'],
            [['status', 'dataplane_status', 'auth_status'], 'string'],
            [['start', 'finish'], 'safe'],
            [['reservation_id'], 'integer'],
            [['external_id'], 'string', 'max' => 65],
            [['external_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_id' => Yii::t('circuits', 'Connection ID'),
            'status' => Yii::t("circuits", 'Reservation Status'),
            'dataplane_status' =>  Yii::t("circuits", 'Connectivity Status'),
            'auth_status' =>  Yii::t("circuits", "Authorization Status"),
            'start' =>  Yii::t("circuits", 'Start'),
            'finish' =>  Yii::t("circuits", 'Finish'),
            'reservation_id' => 'Reservation ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservation()
    {
        return $this->hasOne(Reservation::className(), ['id' => 'reservation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizations()
    {
        return $this->hasMany(ConnectionAuth::className(), ['id' => 'connection_id']);
    }
    
    public function requestCreate() {
    	$provider = $this->getReservation()->one()->getProvider()->one();
    	$provider->requestCreate($this);
    }
    
    public function requestCommit() {
    	$provider = $this->getReservation()->one()->getProvider()->one();
    	$provider->requestCommit($this);
    }
    
    public function requestReadPath() {
    	$provider = $this->getReservation()->one()->getProvider()->one();
    	$provider->requestReadPath($this);
    }
    
    public function requestProvision() {
    	$provider = $this->getReservation()->one()->getProvider()->one();
    	$provider->requestProvision($this);
    }
    
    public function requestCancel() {
    	$this->status = self::STATUS_CANCEL_REQ;
    	$this->save();
    
    	$provider = $this->getReservation()->one()->getProvider()->one();
    	$provider->requestCancel($this);
    }
    
    public function confirmCreate() {
    	$this->status = self::STATUS_CREATED;
    	$this->save();
    }
    
    public function confirmCreatePath() {
    	$this->status = self::STATUS_CONFIRMED;
    	$this->save();
    	
    	//Path encontrado pelo provider, temos que submeter para obter o caminho
    	$this->requestCommit();
    }
    
	public function confirmCancel() {
		$this->status = self::STATUS_CANCELLED;
		$this->save();
	} 
	
	public function confirmCommit() {
		$this->status = self::STATUS_SUBMITTED;
		$this->save();
		
		//Depois de submetido podemos solicitar o caminho encontrado pelo provider
		$this->requestReadPath();
	}
	
	//path confirmado. Se for uma reserva normal, solicitar autorizacao para provisionamento
	public function confirmReadPath() {
		if ($this->getReservation()->one()->type == Reservation::TYPE_NORMAL) {	
			
			$this->requestAuthorization();
		} 
	}
	
	public function confirmProvision() {
		$this->status = self::STATUS_PROVISIONED;
		$this->save();
	}
	
	public function failedCreate() {
		$this->status = self::STATUS_FAILED_CREATE;
		$this->save();
	}
	
	public function failedCreatePath() {
		$this->status = self::STATUS_FAILED_CONFIRM;
		$this->save();
	}
	
	public function failedCommit() {
		$this->status = self::STATUS_FAILED_SUBMIT;
		$this->save();
	}
	
	public function failedProvision() {
		$this->status = self::STATUS_FAILED_PROVISION;
		$this->save();
	}
	
	public function failedCancel() {
		$this->status = self::STATUS_FAILED_CANCEL;
		$this->save();
	}
	
    public function getStatus() {
    	switch($this->status) {
    		case self::STATUS_PENDING: 			return Yii::t("circuits", "Pending");
			case self::STATUS_CREATED : 		return Yii::t("circuits", "Requesting path");
			case self::STATUS_CONFIRMED : 		return Yii::t("circuits", "Path found");
			case self::STATUS_SUBMITTED : 		return Yii::t("circuits", "Waiting authorization");
			case self::STATUS_PROVISIONED : 	return Yii::t("circuits", "Provisioned");
			case self::STATUS_CANCEL_REQ : 		return Yii::t("circuits", "Cancel requested");
			case self::STATUS_CANCELLED : 		return Yii::t("circuits", "Cancelled");
			case self::STATUS_FAILED_CREATE: 	return Yii::t("circuits", "Request rejected");
			case self::STATUS_FAILED_CONFIRM : 	return Yii::t("circuits", "Path not found");
			case self::STATUS_FAILED_SUBMIT : 	return Yii::t("circuits", "Confirm failed");
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
    	}
    }
    
    public function setActiveDataStatus($bool) {
    	if ($bool) {
    		$this->dataplane_status = self::DATA_STATUS_ACTIVE;
    		return;
    	}
    	$this->dataplane_status = self::DATA_STATUS_INACTIVE;
    }
    
    public function isCancelStatus() {
    	return $this->status == "CANCELLED" || $this->status == "CANCEL REQUESTED";
    }
    
    public static function continueWorkflows($id, $asking = true){
    	Yii::trace("CONTINUA WORKFLOWS");
    	
    	$paths = ConnectionPath::find()->select('DISTINCT `domain`')->where(['conn_id' => $id])->all();
    	Yii::trace("Dominios envolvidos:");
    	foreach($paths as $path) Yii::trace($path->domain);
    	
    	//Analisa se existem pedidos em espera. Neste momento, realiza as perguntas aos admins.
    	foreach($paths as $path){
    		if(Connection::findOne(['id' => $id])->auth_status == 'DENIED') break; //Para quando ja negou.
    		else{
    			$domain = Domain::findOne(['topology' => $path->domain]);
    			if(isset($domain)) BpmFlow::doRequest($id, $domain->topology, $asking);
    		}
    		if(ConnectionAuth::find()->where(['connection_id' => $id, 'status' => 'WAITING'])->count() > 0) break; //Se tem uma pergunta ativa.
    	}
    	
    	if(!$asking || ConnectionAuth::find()->where(['connection_id' => $id, 'status' => 'WAITING'])->count() > 0) return;

    	if(Connection::findOne(['id' => $id])->auth_status == 'WAITING'){
    		$conn = Connection::findOne(['id' => $id]);
    		$conn->auth_status = 'AUTHORIZED';
    		if (!$conn->save()){
    		}
    		$conn->requestProvision();
    	}
    }
    
    public function executeWorkflows($id){
    	$paths = ConnectionPath::find()->select('DISTINCT `domain`')->where(['conn_id' => $id])->all();
    	Yii::trace("Dominios envolvidos:");
    	foreach($paths as $path) Yii::trace($path->domain);
    
    	//Cria execução dos Workflows
    	foreach($paths as $path){ //Utiliza unique para não executar duas vezes em intradominios.
    		$domain = Domain::findOne(['topology' => $path->domain]);
	    	if(isset($domain)) BpmFlow::startFlow($id, $domain->topology);
	    	if(Connection::findOne(['id' => $id])->auth_status != 'WAITING') break; //Para quando ja negou.
    	}

    	//Executa primeira vez IMPEDINDO que sejam feitas perguntas.
    	//Sai caso aceite, rejeite ou entre em modo de espera para perguntar.
    	Connection::continueWorkflows($id, false);
    	
    	//Executa segunda vez, liberando as perguntas.
    	Connection::continueWorkflows($id);
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