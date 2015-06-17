<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\BpmFlow;
use app\models\BpmWorkflow;
use app\models\BpmNode;
use app\models\ConnectionAuth;
use app\models\Connection;
use app\models\Reservation;


define("authorized", 'AUTHORIZED');
define("denied", 'DENIED');

/**
 * This is the model class for table "meican_bpm_flow_control".
 *
 * @property integer $id
 * @property integer $connection_id
 * @property integer $workflow_id
 * @property integer $domain_id
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
            [['connection_id', 'workflow_id', 'domain_id', 'node_id', 'type'], 'required'],
            [['connection_id', 'workflow_id', 'domain_id', 'node_id'], 'integer'],
            [['type', 'value', 'operator', 'status'], 'string']
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
            'domain_id' => 'Domain ID',
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
    
    /**
     * 
     * @param unknown $connection_id
     * @param unknown $domain_id
     */
    public static function startFlow($connection_id, $domain_id){   	
    	$domain = Domain::findOne(['id' => $domain_id]);
    	$workflow = BpmWorkflow::findOne(['domain_id' => $domain_id, 'active' => 1]);
    	
    	Yii::trace("!!!! INICIA WORKFLOW !!!! ");
    	Yii::trace("Connection ID: ".$connection_id);
    	Yii::trace("Domain ID: ".$domain_id);
    	
    	if(isset($workflow)){
    		Yii::trace("Workflow ID: ".$workflow->id);
    		
	    	$initNode = BpmNode::findOne(['workflow_id' => $workflow->id, 'type' => 'New_Request']);

	    	$node = BpmNode::findOne(['id' => $initNode->output_yes]);
	    	$flowLine = new BpmFlow();
	    	$flowLine->node_id = $node->id;
	    	$flowLine->type = $node->type;
	    	$flowLine->value = $node->value;
	    	$flowLine->workflow_id = $workflow->id;
	    	$flowLine->connection_id = $connection_id;
	    	$flowLine->domain_id = $domain_id;
	    	if($flowLine->type == 'Request_Group_Authorization' || $flowLine->type == 'Request_User_Authorization') $flowLine->status = 'WAITING';
	    	else $flowLine->status = 'READY';
	    	if($node->operator != null) $flowLine->operator = $node->operator;    		
	    	if (!$flowLine->save()){
	    		Yii::$app->getSession()->setFlash('error', 'Unsuccessful save');
	    	}
		    return;
    	}
    	else Yii::trace("Sem Workflow ATIVO.");
    	
    	if($domain->default_policy == 'ACCEPT_ALL'){
    		Yii::trace("ACEITO pela POLITICA PADRÃO.");
	    	$auth = new ConnectionAuth();
	    	$auth->domain_id = $domain_id;
	    	$auth->status = 'AUTHORIZED';
	    	$auth->type = 'WORKFLOW';
	    	$auth->connection_id = $connection_id;
	    	$auth->save();
    	}
    	else {
    		Yii::trace("NEGADO pela POLITICA PADRÃO.");
    		BpmFlow::deleteAll(['connection_id' => $connection_id]);
	    	$conn = Connection::findOne(['id' => $connection_id]);
	    	$conn->auth_status = denied;
	    	if (!$conn->save()){
    		}
			$auth = new ConnectionAuth();
    		$auth->domain_id = $domain_id;
    		$auth->status = 'DENIED';
    		$auth->type = 'WORKFLOW';
    		$auth->connection_id = $connection_id;
    		$auth->save();
    	}
    	
    }
    
    /**
     * 
     * @param unknown $connection_id
     * @param unknown $domain_id
     * @param unknown $asking
     */
    public static function doRequest($connection_id, $domain_id, $asking){
    	Yii::trace("!!!! CONTINUA WORKFLOW CASO NECESSÁRIO !!!! ");
    	Yii::trace("Connection ID: ".$connection_id);
    	Yii::trace("Domain ID: ".$domain_id);
    	if(BpmFlow::find()->where(['domain_id' => $domain_id, 'connection_id' => $connection_id])->count() > 0){
    		$flow = BpmFlow::findOne(['domain_id' => $domain_id, 'connection_id' => $connection_id]);
    		if($asking){
    			Yii::trace("Perguntas habilitadas");
    			$flow->status = 'READY';
    			$flow->save();
    		}
    		while(BpmFlow::execute($connection_id, $domain_id));
    	}

    }
    
    /**
     * 
     * @param unknown $connection_id
     * @param unknown $domain_id
     * @param unknown $response
     */
    public static function response($connection_id, $domain_id, $response){
    	Yii::trace("!!!! RECEBEU RESPOSTA !!!! ");
    	Yii::trace("Connection ID: ".$connection_id);
    	Yii::trace("Domain ID: ".$domain_id);
    	Yii::trace("Response: ".$response);
    	
    	$flow = BpmFlow::findOne(['domain_id' => $domain_id, 'connection_id' => $connection_id]);
    	$flow->status = $response;
    	$flow->save();
    
    	//Flow loop
	    while(BpmFlow::execute($connection_id, $domain_id));
	    
	    Connection::continueWorkflows($connection_id);
	    
    }

    /**
     * 
     * @param unknown $connection_id
     * @param unknown $domain_id
     * @return boolean
     */
    public static function execute($connection_id, $domain_id){

    	$flows = BpmFlow::find()->where(['domain_id' => $domain_id, 'connection_id' => $connection_id]);
    	
    	
    	
    	if($flows->count() == 0) return false;
    	
    	foreach($flows->all() as $flow){
    		$connection = Connection::findOne(['id' => $connection_id]);
    		$reservation = Reservation::findOne(['id' => $connection->reservation_id]);

    		switch ($flow->type) {
    			
    			//Domain
    			case 'Domain':
    				if($flow->status == 'READY') BpmFlow::checkDomain($flow, $connection);
    				if($flow->status != 'READY') BpmFlow::nextNodes($flow);
    				break;
    				
    			//User
    			case 'User':
    				if($flow->status == 'READY') BpmFlow::checkUser($flow, $reservation);
    				if($flow->status != 'READY') BpmFlow::nextNodes($flow);
    				break;
    				
    			//Bandwidth
    			case 'Bandwidth':
    				if($flow->status == 'READY') BpmFlow::checkBandwidth($flow, $reservation);
    				if($flow->status != 'READY') BpmFlow::nextNodes($flow);
    				break;
    				
    			//Request_User_Authorization
    			case 'Request_User_Authorization':
    				if($flow->status == 'WAITING') return false;
    				else{
	    				if($flow->status == 'READY') return BpmFlow::createUserAuth($flow, $reservation);
	    				if($flow->status != 'READY') BpmFlow::nextNodes($flow);
    				}
    				break;
    				
    			//Request_Group_Autorization
    			case 'Request_Group_Authorization':
    				if($flow->status == 'WAITING') return false;
    				else{
	    				if($flow->status == 'READY') return BpmFlow::createGroupAuth($flow, $reservation);
	    				if($flow->status != 'READY') BpmFlow::nextNodes($flow);
    				}
    				break;
    				
    			//Hour
    			case 'Hour':
    				if($flow->status == 'READY') BpmFlow::checkHour($flow, $reservation);
    				if($flow->status != 'READY') BpmFlow::nextNodes($flow);
    				break;
    				
    			//WeekDay
    			case 'WeekDay':
    				if($flow->status == 'READY') BpmFlow::checkWeekday($flow, $connection);
    				if($flow->status != 'READY') BpmFlow::nextNodes($flow);
    				break;
    				
    			//Duration
    			case 'Duration':
    				if($flow->status == 'READY') BpmFlow::checkDuration($flow, $connection);
    				if($flow->status != 'READY') BpmFlow::nextNodes($flow);
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
    	if($flow->status == 'YES') $output_way ='output_yes';
    	else $output_way = 'output_no';

    	$actualNode = BpmNode::findOne(['id' => $flow->node_id]);
    	
    	$node = BpmNode::findOne(['id' => $actualNode->$output_way]);
    	
    	$flowLine = new BpmFlow();
    	$flowLine->node_id = $node->id;
    	$flowLine->type = $node->type;
    	if($node->value != null) $flowLine->value = $node->value;
    	$flowLine->workflow_id = $flow->workflow_id;
    	$flowLine->connection_id = $flow->connection_id;
    	$flowLine->domain_id = $flow->domain_id;
    	if($flowLine->type == 'Request_Group_Authorization' || $flowLine->type == 'Request_User_Authorization') $flowLine->status = 'WAITING';
    	else $flowLine->status = 'READY';
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
	    	if(BpmFlow::find()->where(['domain_id' => $flow->domain_id, 'connection_id' => $connection_id])->count() == 0){
	    		BpmFlow::deleteAll(['connection_id' => $connection_id]);
	    		$conn = Connection::findOne(['id' => $connection_id]);
	    		$conn->auth_status = denied;
	    		if (!$conn->save()){
    				Yii::error('Unsuccesful save in Request');
    			}
				$auth = new ConnectionAuth();
    			$auth->domain_id = $flow->domain_id;
    			$auth->status = 'DENIED';
    			$auth->type = 'WORKFLOW';
    			$auth->manager_workflow_id = $flow->workflow_id;
    			$auth->connection_id = $connection_id;
    			$auth->save();
	    	}
    	}
    	else {
    		$auth = new ConnectionAuth();
    		$auth->domain_id = $flow->domain_id;
    		$auth->status = 'AUTHORIZED';
    		$auth->type = 'WORKFLOW';
    		$auth->manager_workflow_id = $flow->workflow_id;
    		$auth->connection_id = $connection_id;
    		$auth->save();
    	}
    }
    
    public function createGroupAuth($flow, $reservation){
    	Yii::trace("Criando Request Group Authorization");
    	$auth = new ConnectionAuth();
    	$auth->domain_id = $flow->domain_id;
    	$auth->status = 'WAITING';
    	$auth->type = 'GROUP';
    	$auth->manager_group_id = $flow->value;
    	$auth->connection_id = $flow->connection_id;
    	$auth->save();
    	return false;
    }
    
    public function createUserAuth($flow, $reservation){
    	Yii::trace("Criando Request User Authorization");
    	$auth = new ConnectionAuth();
    	$auth->domain_id = $flow->domain_id;
    	$auth->status = 'WAITING';
    	$auth->type = 'USER';
    	$auth->manager_user_id = $flow->value;
    	$auth->connection_id = $flow->connection_id;
    	$auth->save();
    	return false;
    }
    
    public function checkWeekday($flow, $connection){
    	Yii::trace("Testando Dia da Semana");
    	if($flow->value == Yii::$app->formatter->asDate($connection->start, 'EEEE') && $flow->value == Yii::$app->formatter->asDate($connection->finish, 'EEEE')) $flow->status = 'YES'; //Standart exit way;
    	else{
    		Yii::trace("Não passou em DIA DA SEMANA");
    		$flow->status = 'NO';
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
    			if($time <= $value[0]) $accept = true;
    			break;
    			 
    		case '> ':
    			if($time > $value[0]) $accept = true;
    			break;
    	
    		case '>= ':
    			if($time >= $value[0]) $accept = true;
    			break;
    	
    		case '== ':
    			if($time == $value[0]) $accept = true;
    			break;
    	}
    	 
    	if($accept) $flow->status = 'YES';
    	else{
    		Yii::trace("Não passou em DURAÇÃO");
    		$flow->status = 'NO';
    	}
    }


    public function checkDomain($flow, $connection){
    	Yii::trace("Testando Domain");
    	switch($flow->operator){
    		case 'source':
    			$domain = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => 0])->domain;
    			break;
    			
    		case 'previous':
    			$thisDomain = Domain::findOne([$flow->domain_id]);
    			$cp = ConnectionPath::findOne(['conn_id' => $connection->id, 'domain' => $thisDomain->topology]);
    			if(!isset($cp)){ //Se dominio deletado
    				$flow->status = 'YES';
    				return;
    			}
    			$domain = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => $cp->path_order-1]);
    			if(!isset($domain)){ //Se dominio deletado
    				$flow->status = 'YES';
    				return;
    			}
    			$domain = $domain->domain;
    			break;
    			
    		case 'next':
    			$thisDomain = Domain::findOne([$flow->domain_id]);
    			$cp = ConnectionPath::findOne(['conn_id' => $connection->id, 'domain' => $thisDomain->topology]);
    			if(!isset($cp)){ //Se dominio deletado
    				$flow->status = 'YES';
    				return;
    			}
    			$domain = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => $cp->path_order+1])->domain;
    			if(!isset($domain)){ //Se dominio deletado
    				$flow->status = 'YES';
    				return;
    			}
    			$domain = $domain->domain;
    			break;
    			
    		case 'destination':
    			$path_order = ReservationPath::find()->where(['conn_id' => $connection->id])->count()-1;
    			$domain = ConnectionPath::findOne(['conn_id' => $connection->id, 'path_order' => $path_order])->domain;
    			break;
    	}
    	
    	$compareDomain = Domain::findOne(['topology' => $domain]);
    	
    	if(isset($compareDomain) && $flow->value == $compareDomain->id) $flow->status = 'YES';
    	else{
    		Yii::trace("Não passou em DOMAIN");
    		$flow->status = 'NO';
    	}
    }
    
    public function checkUser($flow, $reservation){
    	Yii::trace("Testando User");
    	if($flow->value == $reservation->request_user_id) $flow->status = 'YES';
    	else{
    		Yii::trace("Não passou em USER");
    		$flow->status = 'NO';
    	}
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
    	
    	if($accept) $flow->status = 'YES';
    	else{
    		Yii::trace("Não passou em BANDWIDTH");
    		$flow->status = 'NO';
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
    
    	if($within_interval) $flow->status = 'YES';
    	else{
    		Yii::trace("Não passou em HORA");
    		$flow->status = 'NO';
    	}
    }

}