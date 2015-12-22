<?php

namespace meican\models;

use Yii;
use yii\data\ActiveDataProvider;
use meican\components\DateUtils;
use meican\congtrollers\RbacController;

/**
 * This is the model class for table "meican_bpm_flow_control".
 *
 * @property integer $id
 * @property integer $connection_id
 * @property integer $workflow_id
 * @property string $domain
 * @property integer $node_id
 * @property string $type
 * @property string $value
 * @property string $operator
 * @property string $status
 *
 * @property BpmNode $node
 * @property Connection $connection
 * @property Domain $domain
 * @property BpmWorkflow $workflow
 */
class BpmFlow extends \yii\db\ActiveRecord
{
	
	const STATUS_WAITING = "WAITING";
	const STATUS_READY = "READY";
	const STATUS_YES = "YES";
	const STATUS_NO = "NO";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meican_bpm_flow_control';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['connection_id', 'workflow_id', 'domain', 'node_id', 'type'], 'required'],
            [['connection_id', 'workflow_id', 'node_id'], 'integer'],
            [['type', 'value', 'operator', 'status'], 'string'],
        	[['domain'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'connection_id' => 'Connection ID',
            'workflow_id' => 'Workflow ID',
            'domain' => 'Domain',
            'node_id' => 'Node ID',
            'type' => 'Type',
            'value' => 'Value',
            'operator' => 'Operator',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(BpmBpmNode::className(), ['id' => 'node_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConnection()
    {
        return $this->hasOne(Reservation::className(), ['id' => 'connection_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkflow()
    {
        return $this->hasOne(BpmWorkflow::className(), ['id' => 'workflow_id']);
    }
    
    public function removeFlows($connection_id){
    	$flows = BpmFlow::find()->where(['connection_id' => $connection_id])->all();
    	foreach($flows as $flow){
    		$flow->delete();
    	}
    }
    
    /**
     * 
     * @param unknown $connection_id
     * @param unknown $domainTop
     */
    public static function startFlow($connection_id, $domainTop){   	
    	$domain = Domain::findOne(['name' => $domainTop]);
    	$workflow = BpmWorkflow::findOne(['domain' => $domainTop, 'active' => 1]);
    	
    	Yii::trace("!!!! INICIA WORKFLOW !!!! ");
    	Yii::trace("Connection ID: ".$connection_id);
    	Yii::trace("Domain: ".$domainTop);
    	
    	if(isset($workflow) && isset($domain)){
    		Yii::trace("Workflow ID: ".$workflow->id);
    		
	    	$initNode = BpmNode::findOne(['workflow_id' => $workflow->id, 'type' => 'New_Request']);

	    	$node = BpmNode::findOne(['id' => $initNode->output_yes]);
	    	$flowLine = new BpmFlow();
	    	$flowLine->node_id = $node->id;
	    	$flowLine->type = $node->type;
	    	$flowLine->value = $node->value;
	    	$flowLine->workflow_id = $workflow->id;
	    	$flowLine->connection_id = $connection_id;
	    	$flowLine->domain = $domainTop;
	    	if($flowLine->type == 'Request_Group_Authorization' || $flowLine->type == 'Request_User_Authorization') $flowLine->status = self::STATUS_WAITING;
	    	else $flowLine->status = self::STATUS_READY;
	    	if($node->operator != null) $flowLine->operator = $node->operator;    		
	    	if (!$flowLine->save()){
	    		Yii::$app->getSession()->setFlash('error', 'Unsuccessful save');
	    	}
		    return;
    	}
    	else Yii::trace("Sem Workflow ATIVO.");
    	
    	if(!$domain){
    		Yii::trace("Dominio não existe mais na base. ACEITO.");
    	}
    	else if($domain->default_policy == Domain::ACCEPT_ALL){
    		Yii::trace("ACEITO pela POLITICA PADRÃO.");
	    	$auth = new ConnectionAuth();
	    	$auth->domain = $domainTop;
	    	$auth->status = Connection::AUTH_STATUS_APPROVED;
	    	$auth->type = ConnectionAuth::TYPE_WORKFLOW;
	    	$auth->connection_id = $connection_id;
	    	$auth->save();
    	}
    	else {
    		Yii::trace("NEGADO pela POLITICA PADRÃO.");
    		BpmFlow::deleteAll(['connection_id' => $connection_id]);
	    	$conn = Connection::findOne(['id' => $connection_id]);
	    	$conn->auth_status = Connection::AUTH_STATUS_REJECTED;
	    	if (!$conn->save()){
    		}
			$auth = new ConnectionAuth();
    		$auth->domain = $domainTop;
    		$auth->status = Connection::AUTH_STATUS_REJECTED;
    		$auth->type = ConnectionAuth::TYPE_WORKFLOW;
    		$auth->connection_id = $connection_id;
    		$auth->save();
    	}
    }
    
    /**
     * 
     * @param unknown $connection_id
     * @param unknown $domainTop
     * @param unknown $asking
     */
    public static function doRequest($connection_id, $domainTop, $asking){
    	Yii::trace("!!!! CONTINUA WORKFLOW CASO NECESSÁRIO !!!! ");
    	Yii::trace("Connection ID: ".$connection_id);
    	Yii::trace("Domain: ".$domainTop);
    	if(BpmFlow::find()->where(['domain' => $domainTop, 'connection_id' => $connection_id])->count() > 0){
    		$flow = BpmFlow::findOne(['domain' => $domainTop, 'connection_id' => $connection_id]);
    		if($asking){
    			Yii::trace("Perguntas habilitadas");
    			$flow->status = self::STATUS_READY;
    			$flow->save();
    		}
    		while(BpmFlow::execute($connection_id, $domainTop));
    	}

    }
    
    /**
     * 
     * @param unknown $connection_id
     * @param unknown $domainTop
     * @param unknown $response
     */
    public static function response($connection_id, $domainTop, $response){
    	Yii::trace("!!!! RECEBEU RESPOSTA !!!! ");
    	Yii::trace("Connection ID: ".$connection_id);
    	Yii::trace("Domain: ".$domainTop);
    	Yii::trace("Response: ".$response);
    	
    	$flow = BpmFlow::findOne(['domain' => $domainTop, 'connection_id' => $connection_id]);
    	$flow->status = $response;
    	$flow->save();
    
    	//Flow loop
	    while(BpmFlow::execute($connection_id, $domainTop));
	    
	    Connection::continueWorkflows($connection_id);
	    
    }

    /**
     * 
     * @param unknown $connection_id
     * @param unknown $domainTop
     * @return boolean
     */
    public static function execute($connection_id, $domainTop){

    	$flows = BpmFlow::find()->where(['domain' => $domainTop, 'connection_id' => $connection_id]);
    	
    	if($flows->count() == 0) return false;
    	
    	foreach($flows->all() as $flow){
    		$connection = Connection::findOne(['id' => $connection_id]);
    		$reservation = Reservation::findOne(['id' => $connection->reservation_id]);

    		switch ($flow->type) {
    			
    			//Domain
    			case 'Domain':
    				if($flow->status == self::STATUS_READY) BpmFlow::checkDomain($flow, $connection);
    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				break;
    				
    			//User
    			case 'User':
    				if($flow->status == self::STATUS_READY) BpmFlow::checkUser($flow, $reservation);
    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				break;
    				
    			//Group
    			case 'Group':
    				if($flow->status == self::STATUS_READY) BpmFlow::checkGroup($flow, $reservation);
    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				break;
    				
    			//Bandwidth
    			case 'Bandwidth':
    				if($flow->status == self::STATUS_READY) BpmFlow::checkBandwidth($flow, $reservation);
    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				break;
    				
    			//Request_User_Authorization
    			case 'Request_User_Authorization':
    				if($flow->status == self::STATUS_WAITING) return false;
    				else{
	    				if($flow->status == self::STATUS_READY) return BpmFlow::createUserAuth($flow, $reservation);
	    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				}
    				break;
    				
    			//Request_Group_Autorization
    			case 'Request_Group_Authorization':
    				if($flow->status == self::STATUS_WAITING) return false;
    				else{
	    				if($flow->status == self::STATUS_READY) return BpmFlow::createGroupAuth($flow, $reservation);
	    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				}
    				break;
    				
    			//Hour
    			case 'Hour':
    				if($flow->status == self::STATUS_READY) BpmFlow::checkHour($flow, $reservation);
    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				break;
    				
    			//WeekDay
    			case 'WeekDay':
    				if($flow->status == self::STATUS_READY) BpmFlow::checkWeekday($flow, $connection);
    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				break;
    				
    			//Duration
    			case 'Duration':
    				if($flow->status == self::STATUS_READY) BpmFlow::checkDuration($flow, $connection);
    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				break;
    				
    			//Domain
    			case 'Device':
    				if($flow->status == self::STATUS_READY) BpmFlow::checkDevice($flow, $connection);
    				if($flow->status != self::STATUS_READY) BpmFlow::nextNodes($flow);
    				break;
    			
    			//Accept_Automatically	
    			case 'Accept_Automatically':
    				Yii::trace("Requisição ACEITA");
    				BpmFlow::removeFlow($flow);
    				return false;
    				break;
    			
    			//Deny_Automatically
    			case 'Deny_Automatically':
    				Yii::trace("Requisição NEGADA");
    				BpmFlow::removeFlow($flow);
    				return false;
    				break;
    		}
    	}
    	
    	return true;
    }
    
    /**
     * 
     * @param BpmFlow $flow
     */
    public static function nextNodes($flow){
    	if($flow->status == self::STATUS_YES) $output_way ='output_yes';
    	else $output_way = 'output_no';

    	$actualNode = BpmNode::findOne(['id' => $flow->node_id]);
    	
    	$node = BpmNode::findOne(['id' => $actualNode->$output_way]);
    	
    	$flowLine = new BpmFlow();
    	$flowLine->node_id = $node->id;
    	$flowLine->type = $node->type;
    	if($node->value != null) $flowLine->value = $node->value;
    	$flowLine->workflow_id = $flow->workflow_id;
    	$flowLine->connection_id = $flow->connection_id;
    	$flowLine->domain = $flow->domain;
    	if($flowLine->type == 'Request_Group_Authorization' || $flowLine->type == 'Request_User_Authorization') $flowLine->status = self::STATUS_WAITING;
    	else $flowLine->status = self::STATUS_READY;
    	if($node->operator != null) $flowLine->operator = $node->operator;
    	$flowLine->validate();
    	if (!$flowLine->save()){
    		Yii::$app->getSession()->setFlash('error', 'Unsuccessful save');
    	}
    	$flow->delete();
    }
    
    /**
     * 
     * @param BpmFlow $flow
     */
    public function removeFlow($flow){
    	$type = $flow->type;
    	$connection_id = $flow->connection_id;
    	$flow->delete();
    	if($type != 'Accept_Automatically'){
	    	if(BpmFlow::find()->where(['domain' => $flow->domain, 'connection_id' => $connection_id])->count() == 0){
	    		BpmFlow::deleteAll(['connection_id' => $connection_id]);
	    		$conn = Connection::findOne(['id' => $connection_id]);
	    		$conn->auth_status = Connection::AUTH_STATUS_REJECTED;
	    		if (!$conn->save()){
    				Yii::error('Unsuccesful save in Request');
    			}
				$auth = new ConnectionAuth();
    			$auth->domain = $flow->domain;
    			$auth->status = Connection::AUTH_STATUS_REJECTED;
    			$auth->type = ConnectionAuth::TYPE_WORKFLOW;;
    			$auth->manager_workflow_id = $flow->workflow_id;
    			$auth->connection_id = $connection_id;
    			$auth->save();
    			
    			Notification::createConnectionNotification($connection_id);
	    	}
    	}
    	else {
    		$auth = new ConnectionAuth();
    		$auth->domain = $flow->domain;
    		$auth->status = Connection::AUTH_STATUS_APPROVED;
    		$auth->type = ConnectionAuth::TYPE_WORKFLOW;
    		$auth->manager_workflow_id = $flow->workflow_id;
    		$auth->connection_id = $connection_id;
    		$auth->save();
    	}
    }
    
    public function createGroupAuth($flow, $reservation){
    	Yii::trace("Criando Request Group Authorization");
    	
    	//Confere se o grupo ja respondeu exatamente mesma requisição, se sim, não questiona novamente.
    	$auth = ConnectionAuth::findOne(['type' => ConnectionAuth::TYPE_GROUP, 'domain' => $flow->domain, 'manager_group_id' => $flow->value, 'connection_id' => $flow->connection_id]);
    	 
    	if($auth) return true;
    	
    	$auth = new ConnectionAuth();
    	$auth->domain = $flow->domain;
    	$auth->status = self::STATUS_WAITING;
    	$auth->type = ConnectionAuth::TYPE_GROUP;
    	$auth->manager_group_id = $flow->value;
    	$auth->connection_id = $flow->connection_id;
    	$auth->save();
    	
    	Notification::createGroupAuthNotification($flow->value, $flow->domain, $reservation->id, $auth->id);
    	
    	return false;
    }
    
    public function createUserAuth($flow, $reservation){
    	Yii::trace("Criando Request User Authorization");
    	
    	//Confere se o usuário ja respondeu exatamente mesma requisição, se sim, não questiona novamente.
    	$auth = ConnectionAuth::findOne(['type' => ConnectionAuth::TYPE_USER, 'domain' => $flow->domain, 'manager_user_id' => $flow->value, 'connection_id' => $flow->connection_id]);
    	
    	if($auth) return true;
    	
    	//Confere se usuário requisitante é o mesmo que deve responder. Se sim, não pergunta, considera aceito.
    	if($flow->value == $reservation->request_user_id){
    		$flow->status = self::STATUS_YES;
    		$flow->save();
    		return true;
    	}
	    
    	$auth = new ConnectionAuth();
	    $auth->domain = $flow->domain;
	    $auth->status = Connection::AUTH_STATUS_PENDING;
	    $auth->type = ConnectionAuth::TYPE_USER;
	    $auth->manager_user_id = $flow->value;
	    $auth->connection_id = $flow->connection_id;
	    $auth->save();
	    
	    Notification::createUserAuthNotification($flow->value, $flow->domain, $reservation->id, $auth->id);
    	
    	return false;
    }
    
    public function checkWeekday($flow, $connection){
    	Yii::trace("Testando Dia da Semana");
    	if($flow->value == Yii::$app->formatter->asDate($connection->start, 'EEEE') && $flow->value == Yii::$app->formatter->asDate($connection->finish, 'EEEE')) $flow->status = self::STATUS_YES; //Standart exit way;
    	else{
    		Yii::trace("Não passou em DIA DA SEMANA");
    		$flow->status = self::STATUS_NO;
    	}
    }
    
    public function checkDuration($flow, $connection){
    	Yii::trace("Testando Duração");
    	 
    	$start = strtotime($connection->start);
    	$finish = strtotime($connection->finish);
    	$mins = ($finish - $start) / 60;

    	$value = $flow->value;
    	$value = explode("_", $value);
    	
    	if($value[1] == "hours"){
    		$hours = 0;
    		while($mins > 59){
    			$hours++;
    			$mins-=60;
    		}
    		$time = $hours;
    	}
    	else $time = $mins;
    	
    	$accept = false;
    	switch ($flow->operator) {
    		case '< ':
    			if($time < $value[0]) $accept = true;
    			break;
    	
    		case '<= ':
    			if($time <= $value[0]){
    				if($value[1] == "hours"){
    					if($mins == 0) $accept = true;
    				}
    				else $accept = true;
    			}
    			break;
    			 
    		case '> ':
    			if($time > $value[0]) $accept = true;
    			else if($value[1] == "hours" && $time == $value[0] && $mins > 0) $accept = true;
    			break;
    	
    		case '>= ':
    			if($time >= $value[0]) $accept = true;
    			break;
    	
    		case '== ':
    			if($time == $value[0]){
    				if($value[1] == "hours"){
    					if($mins == 0) $accept = true;
    				}
    				else $accept = true;
    			}
    			break;
    	}
    	 
    	if($accept) $flow->status = self::STATUS_YES;
    	else{
    		Yii::trace("Não passou em DURAÇÃO");
    		$flow->status = self::STATUS_NO;
    	}
    }

    public function checkDevice($flow, $connection){
    	Yii::trace("Testando Device");
    	$haveDevice = false;
    	$paths = ConnectionPath::find()->where(['conn_id' => $connection->id, 'domain' => $flow->domain])->all();
    	
    	foreach($paths as $path){
    		$port = Port::findOne(['urn' => $path->port_urn]);
    		if(isset($port)){
    			$device = $port->getDevice()->one();
    			if(isset($device))
    				if($device->id == $flow->value) $haveDevice = true;
    		}
    		
    		if($haveDevice == true) {
	    		$flow->status = self::STATUS_YES;
	    		return;
	    	}
    	}
    	
    	Yii::trace("Não passou em DEVICE");
    	$flow->status = self::STATUS_NO;
    }

    public function checkDomain($flow, $connection){
    	Yii::trace("Testando Domain");
    	switch($flow->operator){
    		case 'source':
    			$domain = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => 0])->domain;
    			break;
    			
    		case 'previous':
    			$cp = ConnectionPath::findOne(['conn_id' => $connection->id, 'domain' => $flow->domain]);
    			if(!isset($cp)){ //Se dominio deletado
    				$flow->status = self::STATUS_YES;
    				return;
    			}
    			$domain = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => $cp->path_order-1]);
    			if(!isset($domain)){ //Se dominio deletado
    				$flow->status = self::STATUS_YES;
    				return;
    			}
    			$domain = $domain->domain;
    			break;
    			
    		case 'next':
    			$cp = ConnectionPath::findOne(['conn_id' => $connection->id, 'domain' => $flow->domain]);
    			if(!isset($cp)){ //Se dominio deletado
    				$flow->status = self::STATUS_YES;
    				return;
    			}
    			$domain = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => $cp->path_order+1]);
    			if(!isset($domain)){ //Se dominio deletado
    				$flow->status = self::STATUS_YES;
    				return;
    			}
    			$domain = $domain->domain;
    			break;
    			
    		case 'destination':
    			$path_order = ConnectionPath::find()->where(['conn_id' => $connection->id])->count()-1;
    			$domain = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => $path_order])->domain;
    			break;
    	}

    	if(isset($domain) && $flow->value == $domain) $flow->status = self::STATUS_YES;
    	else{
    		Yii::trace("Não passou em DOMAIN");
    		$flow->status = self::STATUS_NO;
    	}
    }
    
    public function checkUser($flow, $reservation){
    	Yii::trace("Testando User");
    	if($flow->value == $reservation->request_user_id) $flow->status = self::STATUS_YES;
    	else{
    		Yii::trace("Não passou em USER");
    		$flow->status = self::STATUS_NO;
    	}
    }
    
    public function checkGroup($flow, $reservation){
    	Yii::trace("Testando Grupo");
    	$user = User::findOne($reservation->request_user_id);

    	$roles = $user->getUserDomainRoles()->all();
    	
    	foreach($roles as $role){
    		$group = $role->getGroup();
    		if($role->domain == null || $role->domain == $flow->domain){
	    		if($flow->value == $group->id){
	    			$flow->status = self::STATUS_YES;
	    			return;
	    		}
    		}
    	}
    	
    	Yii::trace("Não passou em GROUP");
    	$flow->status = self::STATUS_NO;
    }
    
    public function checkBandwidth($flow, $reservation){
    	Yii::trace("Testando Bandwidth");
    	$accept = false;
    	switch ($flow->operator) {
    		case '< ':
    			Yii::trace("Testando menor");
    			if($reservation->bandwidth < $flow->value) $accept = true;
    			break;
    				
    		case '<= ':
    			Yii::trace("Testando menor ou igual");
    			if($reservation->bandwidth <= $flow->value) $accept = true;
    			break;
    	
    		case '> ':
    			Yii::trace("Testando maior");
    			if($reservation->bandwidth > $flow->value) $accept = true;
    			break;
    				
    		case '>= ':
    			Yii::trace("Testando menor ou igual");
    			if($reservation->bandwidth >= $flow->value) $accept = true;
    			break;
    				
    		case '== ':
    			Yii::trace("Testando igual");
    			if($reservation->bandwidth == $flow->value) $accept = true;
    			break;
    	}
    	
    	if($accept) $flow->status = self::STATUS_YES;
    	else{
    		Yii::trace("Não passou em BANDWIDTH");
    		$flow->status = self::STATUS_NO;
    	}
    }
    
    public function checkHour($flow, $connection){
    	Yii::trace("Testando Horario");
    	 
    	$start = strtotime($connection->start);
    	$finish = strtotime($connection->finish);
    	$mins = ($finish - $start) / 60;
    	 
    	if($mins < 1440){
    		$value = $flow->value; //Le valor do banco
    		$value = explode("-", $value); //Separa em inicio e fim
    
    		$initLimit = explode(":", $value[0]); //Separa em hora e minuto
    		$finishLimit = explode(":", $value[1]); //Separa em hora e minuto
    
    		$within_interval = true;
    
    		$init = Yii::$app->formatter->asDate($connection->start, "php:H:i");
    		$init = explode(":", $init);
    
    		$finish = Yii::$app->formatter->asDate($connection->finish, "php:H:i");
    		$finish = explode(":", $finish);
    
    		//Intervalo normal, no mesmo dia
    		if($initLimit[0] < $finishLimit[0]){
    			if($init[0] < $initLimit[0]) $within_interval = false;
    			else if($init[0] == $initLimit[0] && $init[1] < $initLimit[1]) $within_interval = false;
    
    			if($finish[0] > $finishLimit[0]) $within_interval = false;
    			else if($finish[0] == $finishLimit[0] && $finish[1] > $finishLimit[1]) $within_interval = false;
    		}
    		//Intervalo com troca de dia
    		else if($initLimit[0] > $finishLimit[0]){
    			$pad = 24-$initLimit[0]; //Encontra o pad que tem que ser somado aos elementos.
    			$initLimit[0] = 24; //Inicio fica em 24
    			$finishLimit[0] += $pad + 24; //Adiciona o pad + 24 horas.
    	   
    			$init[0] += $pad; //Adiciona o pad
    			if($init[0] < 24) $init[0] += 24;
    	   
    			$finish[0] += $pad; + 24; //Adiciona o pad
    			if($finish[0] < 24) $finish[0] += 24;
    	   
    			if($init[0] < $initLimit[0]) $within_interval = false;
    			else if($init[0] == $initLimit[0] && $init[1] < $initLimit[1]) $within_interval = false;
    
    			if($finish[0] > $finishLimit[0]) $within_interval = false;
    			else if($finish[0] == $finishLimit[0] && $finish[1] > $finishLimit[1]) $within_interval = false;
    		}
    		//Horas iguais, confere os minutos
    		else{
    			//Intervalo normal, no mesmo dia
    			if($initLimit[1] < $finishLimit[1]){
    				if($init[0] < $initLimit[0]) $within_interval = false;
    				else if($init[1] < $initLimit[1]) $within_interval = false;
    
    				if($finish[0] > $finishLimit[0]) $within_interval = false;
    				else if($finish[1] > $finishLimit[1]) $within_interval = false;
    			}
    			//Intervalo com troca de dia
    			else if($initLimit[1] > $finishLimit[1]){
    				$pad = 24-$initLimit[0]; //Encontra o pad que tem que ser somado aos elementos.
    				$initLimit[0] = 24; //Inicio fica em 24
    				$finishLimit[0] += $pad + 24; //Adiciona o pad + 24 horas.
    
    				$init[0] += $pad; //Adiciona o pad
    				if($init[0] < 24) $init[0] += 24;
    
    				$finish[0] += $pad; + 24; //Adiciona o pad
    				if($finish[0] < 24) $finish[0] += 24;
    
    				if($init[0] < $initLimit[0]) $within_interval = false;
    				else if($init[0] == $initLimit[0] && $init[1] < $initLimit[1]) $within_interval = false;
    
    				if($finish[0] > $finishLimit[0]) $within_interval = false;
    				else if($finish[0] == $finishLimit[0] && $finish[1] > $finishLimit[1]) $within_interval = false;
    			}
    			//Sem intervalo, hora tem que corresponder exatamente.
    			else{
    				if($init[0] != $initLimit[0] || $init[1] != $initLimit[1]) $within_interval = false;
    				if($finish[0] != $finishLimit[0] || $finish[1] != $finishLimit[1]) $within_interval = false;
    			}
    		}
    	}
    	else $within_interval = false;
    
    	if($within_interval) $flow->status = self::STATUS_YES;
    	else{
    		Yii::trace("Não passou em HORA");
    		$flow->status = self::STATUS_NO;
    	}
    }

}