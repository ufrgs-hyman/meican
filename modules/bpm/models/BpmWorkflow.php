<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\bpm\models;

use Yii;
use meican\bpm\models\GraphNode;
use meican\bpm\models\BpmFlow;
use meican\bpm\models\BpmNode;
use meican\components\DateUtils;
use meican\topology\models\Domain;
use meican\circuits\models\ConnectionAuth;
use meican\base\components\DataUtils;


/**
 * This is the model class for table "{{%bpm_workflow}}".
 *
 * @property string $domain
 * @property string $name
 * @property string $json
 * @property integer $active
 *
 */
class BpmWorkflow extends \yii\db\ActiveRecord
{
	
	const STATUS_ENABLED = "1";
	const STATUS_DISABLED = "0";
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%bpm_workflow}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'domain', 'json', 'active'], 'required'],
            [['active'], 'integer'],
            [['domain', 'name'], 'string', 'max' => 50],
        	[['json'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t("bpm", 'Name'),
            'domain' => Yii::t("bpm", 'Domain'),
            'json' => Yii::t("bpm", 'Json Structure'),
        	'active' => Yii::t("bpm", 'Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['name' => 'domain']);
    }
    
    /**
     * 
     * @param int $id
     * @return boolean
     */
    public static function disable($id){
    	$workflow = BpmWorkflow::findOne(['id' => $id]);
    	$workflow->active = 0;
    	$domain = Domain::findOne(['name' => $workflow->domain]);
    	
    	if(!$domain) return false;
    	
    	if(!$workflow->save()) return false;

    	// Procura por execuções em aberto envolvendo o workflow. 
    	$flows = BpmFlow::find()->where(['workflow_id' => $id])->all();
    	foreach($flows as $flow){
    		$conId = $flow->connection_id;
    		//Deleta o fluxo
    		$flow->delete();
    		//Deleta autorizações
    		ConnectionAuth::deleteAll(['connection_id' => $conId, 'domain' => $domain->name]);
    		//Cria novo fluxo, com workflow desativado
    		BpmFlow::startFlow($conId, $domain->name);
    		//Dispara continuidade dos workflows
    		Connection::continueWorkflows($conId, true);
    	}

    	return true;
    }
    
    public function copy(){
    	$json = json_decode($this->json, true);
    	$json['params']['name'] = "COPY - ".$this->name;
    	
    	$working = json_decode($json['params']['working'], true);
    	$working['properties']['name'] = "COPY - ".$this->name;
    	
    	$json['params']['working'] = json_encode($working);
    	
    	$this->json = json_encode($json);
    	$this->saveWorkflow($type='copy');
    }
    
    public function isDisabled(){
    	if($this->active == 0) return true;
    	else return false;
    }

    /**
     * @var $nodes
     */
    public $nodes;
    
    /**
     * @param string $type
     */
    public function saveWorkflow($type=null) {
    	
    	if($type != 'copy') $request = json_decode($_POST['model'], true);
    	else $request = json_decode($this->json, true);
    
		if($type=='update') $id = $_POST['id'];

    	$params = $request['params'];
    	 
    	$working = json_decode($params['working']);
    	
    	//Read the nodes and check if them are complete
    	$this->nodes = [];
    	$idCount = 0;
    	foreach ($working->modules as $module) {
    		$node = new GraphNode();
    		$node->id = $idCount;
    		$node->setName($module->name);
    		//If are elements that have camps to complete
    		if($node->type > 0 && $node->type < 20){
    			if($node->type == 3){// Filter by bandwidth
    				if(isset($module->value->post)) {
    					if($module->value->post->bandwidth != ""){
    						if(is_numeric($module->value->post->bandwidth)){
    							$aux = $module->value->post->bandwidth;
    							$node->value = $aux;
    							$node->operator = $module->value->post->operator;
    						}
    						else {
    							$msg = Yii::t("bpm", 'Please, insert a numeric bandwidth at the node:')."\n".Yii::t("bpm", $node->getRealName());
    							$response = array('error' => $msg);
    							echo json_encode($response);
    							return;
    						}
    					}
    					else {
    						$msg = Yii::t("bpm", 'Please, insert a bandwidth at the node:')."\n".Yii::t("bpm", $node->getRealName());
    						$response = array('error' => $msg);
    						echo json_encode($response);
    						return;
    					}
    				}
    				else {
    					$msg = Yii::t("bpm", 'Please, complete the node:')."\n".Yii::t("bpm", $node->getRealName());
    					$response = array('error' => $msg);
    					echo json_encode($response);
    					return;
    				}
    			}
    			
    			else if($node->type == 8){ //Filter by duration
    				if(isset($module->value->post)) {
    					if($module->value->post->duration != ""){
    						if(is_numeric($module->value->post->duration)){
    							$node->value = $module->value->post->duration."_".$module->value->post->unit;
    							$node->operator = $module->value->post->operator;
    						}
    						else {
    							$msg = Yii::t("bpm", 'Please, insert a numeric duration at the node:')."\n".Yii::t("bpm", $node->getRealName());
    							$response = array('error' => $msg);
    							echo json_encode($response);
    							return;
    						}
    					}
    					else {
    						$msg = Yii::t("bpm", 'Please, insert a duration at the node:')."\n".Yii::t("bpm", $node->getRealName());
    						$response = array('error' => $msg);
    						echo json_encode($response);
    						return;
    					}
    				}
    				else {
    					$msg = Yii::t("bpm", 'Please, complete the node:')."\n".Yii::t("bpm", $node->getRealName());
    					$response = array('error' => $msg);
    					echo json_encode($response);
    					return;
    				}
    			}

    			else if($node->type == 6){ //Filter by schedule
    				if(isset($module->value->post)) {
    					$date = DateUtils::now();
    					$date = explode(" ", $date);
    					$date = $date[0];
    					
    					$date = explode("-", $date);
    					$date = $date[2]."/".$date[1]."/".$date[0];
    					
    					$init = $module->value->post->init[0];
    					$init .= ":".$module->value->post->init[1];

    					$init = DateUtils::toUTC($date, $init);
    					$init = explode(" ", $init);
    					$init = $init[1];
    					$initAux = explode(":", $init);
    					$init = $initAux[0].':'.$initAux[1];
    					
    					$module->value->post->init[0] = $initAux[0];
    					$module->value->post->init[1] = $initAux[1];
    					
    					$finish = $module->value->post->finish[0];
    					$finish .= ":".$module->value->post->finish[1];
    					
    					$finish = DateUtils::toUTC($date, $finish);
    					$finish = explode(" ", $finish);
    					$finish = $finish[1];
    					$finishAux = explode(":", $finish);
    					$finish = $finishAux[0].':'.$finishAux[1];
    					
    					$module->value->post->finish[0] = $finishAux[0];
    					$module->value->post->finish[1] = $finishAux[1];
    					
    					$timeInterval = $init.'-'.$finish;
    					
    					$node->value = $timeInterval;
    				}
    				else {
    					$msg = Yii::t("bpm", 'Please, complete the node:')."\n".Yii::t("bpm", $node->getRealName());
    					$response = array('error' => $msg);
    					echo json_encode($response);
    					return;
    				}
    			}
    			
    			else if($node->type == 1){ //Filter by domain
    				if(isset($module->value->post)) {
    					$node->operator = $module->value->post->dom_operator;
    					$node->value = $module->value->post->value;
    				}
    				else {
    					$msg = Yii::t("bpm", 'Please, complete the node:')."\n".Yii::t("bpm", $node->getRealName());
    					$response = array('error' => $msg);
    					echo json_encode($response);
    					return;
    				}
    			}
    			
    			else {
    				if(isset($module->value->post)) {
    					$node->value = $module->value->post;
    				} else {
    					$msg = Yii::t("bpm", 'Please, complete the node:')."\n".Yii::t("bpm", $node->getRealName());
    					$response = array('error' => $msg);
    					echo json_encode($response);
    					return;
    				}
    			}
    		}
    		$this->nodes[] = $node;
    		$idCount++;
    	}
    	 
    	//Check if have a New Request and a Accept
    	$haveNewRequest = false;
    	$haveTerminalNode = false;
    	foreach($this->nodes as $node){
    		if($node->type == 0){
    			if($haveNewRequest == false) $haveNewRequest = true;
    			else {
    				$msg = Yii::t("bpm", 'Please, insert just one node ')."\n".Yii::t("bpm", $node->getRealName());
    				$response = array('error' => $msg);
    				echo json_encode($response);
    				return;
    			}
    		}
    		else if($node->type == 20 || $node->type == 30) $haveTerminalNode = true;
    	}
    	if($haveNewRequest == false){
    		$msg = Yii::t("bpm", 'Please, insert a Arriving a New Request node.');
    		$response = array('error' => $msg);
    		echo json_encode($response);
    		return;
    	}
    	if($haveTerminalNode == false){
    		$msg = Yii::t("bpm", 'Please, insert a Authorization Accept or Authorization Denied node.');
    		$response = array('error' => $msg);
    		echo json_encode($response);
    		return;
    	}
    	 
    	//Save wires on their respective nodes
    	foreach ($working->wires as $value) {
    		if($this->nodes[$value->src->moduleId]->type != 0){
    			if(strcmp($value->src->terminal, "_OUTPUT_YES") == 0) $this->nodes[$value->src->moduleId]->addAdjacency($value->tgt->moduleId, 0);
    			else $this->nodes[$value->src->moduleId]->addAdjacency($value->tgt->moduleId, 1);
    		}
    		else $this->nodes[$value->src->moduleId]->addAdjacency($value->tgt->moduleId, 0);
    		$this->nodes[$value->tgt->moduleId]->setEntryConnected();
    	}
    	 
    	//Print the nodes and their adjacencies for check
    	foreach($this->nodes as $node){
    		Yii::trace("Adjacencies node: ".Yii::t("bpm", $node->getRealName()).":");
    		Yii::trace($node->entryConnected);
    		foreach($node->adjacency as $adjacency){
    			Yii::trace($adjacency);
    			
    		}
    	}
    	
    	//Check if all nodes are connected
    	foreach($this->nodes as $node){
    		$res = $node->isConnected();
    		if($res == "notCon"){
    			$msg = Yii::t("bpm", 'Please, connect the node:')."\n".Yii::t("bpm", $node->getRealName());
    			$response = array('error' => $msg);
    			echo json_encode($response);
    			return;
    		}
    		else if($res == "repeated"){
    			$msg = Yii::t("bpm", 'Please, do not connect the two outputs of ').Yii::t("bpm", $node->getRealName()).Yii::t("bpm", ' in the same entry.');
    			$response = array('error' => $msg);
    			echo json_encode($response);
    			return;
    		}
    	}
    	 
    	//Search and remove repeated nodes
    	$AcceptNodeId = -1;
    	$DenyNodeId = -1;
    	foreach($this->nodes as $node){
    		if($node->type == 20){ //Accept
    			if($AcceptNodeId == -1)	$AcceptNodeId = $node->id;
    			else {
    				foreach($this->nodes as $nodeAux){
    					foreach($nodeAux->adjacency as $adjacency){
    						if($adjacency == $node->id){
    							if($nodeAux->type != 0){
    								$nodeAux->addAdjacency($AcceptNodeId, $nodeAux->removeAdjacency($adjacency));
    							}
    							else {
    								$nodeAux->removeAdjacency($adjacency);
    								$nodeAux->addAdjacency($AcceptNodeId, 0);
    							}
    						}
    					}
    				}
    				unset($this->nodes[$node->id]);
    			}
    		}
    
    		else if($node->type == 30){ //Deny
    			if($DenyNodeId == -1)	$DenyNodeId = $node->id;
    			else {
    				foreach($this->nodes as $nodeAux){
    					foreach($nodeAux->adjacency as $adjacency){
    						if($adjacency == $node->id){
    							if($nodeAux->type != 0){
    								$way = $nodeAux->removeAdjacency($adjacency);
    								$nodeAux->addAdjacency($DenyNodeId, $way);
    							}
    							else {
    								$nodeAux->removeAdjacency($adjacency);
    								$nodeAux->addAdjacency($DenyNodeId);
    							}
    						}
    					}
    				}
    				unset($this->nodes[$node->id]);
    			}
    		}
    	}
    	 
    	//Print the nodes and their adjacencies for check
    	foreach($this->nodes as $node){
    		Yii::trace("Adjacencies node: ".Yii::t("bpm", $node->getRealName()).":");
    		foreach($node->adjacency as $adjacency){
    			Yii::trace($adjacency);
    		}
    	}
    	 
    	//Save workflow in database
    	$work = new BpmWorkflow;
    	//If update remove depreciated Workflow
    	if($type == 'update'){
    		$workAux = BpmWorkflow::findOne(['id' => $id]);
    		if($workAux != null){
    			$other = BpmWorkflow::findOne(['name' => $params['name'], 'domain' => $working->properties->domains_owner]);
    			if(isset($other))
    				if($other->id != $id){
	    				$response = array('error' => Yii::t("bpm", 'This name already exist in this Domain.'));
	    				echo json_encode($response);
	    				return;
	    			}
    			$work=$workAux;
    		}
    	}
    	else if(BpmWorkflow::findOne(['name' => $params['name'], 'domain' => $working->properties->domains_owner])){
    		$response = array('error' => Yii::t("bpm", 'This name already exist in this Domain.'));
    		echo json_encode($response);
    		return;
    	}

    	$work->name = $params['name'];
    	$work->domain = $working->properties->domains_owner;
    	$work->active = 0;
    	
    	//Monta json
		$request['params']['working'] = json_encode($working);
    	$json_aux = json_encode($request);
    	
    	if($type != 'copy') $work->json = json_encode($request);
    	else $work->json = $json_aux;
    	
    	if (!$work->save()) {
    		$response = array('error' => "Not saved.");
    		Yii::trace($work);
    		Yii::trace($work->getErrors());
    		echo json_encode($response);
    		return;
    	}
    
    	//Get workflow_id in DB
    	$db_workflow_id = BpmWorkflow::findOne(['name' => $work->name, 'domain' => $working->properties->domains_owner])->id;
    
    	//Save nodes
    	BpmNode::deleteAll(['in', 'workflow_id', $work->id]);
    	 
    	foreach($this->nodes as $node){
    		$nodeDB = new BpmNode();
    		$nodeDB->workflow_id = $db_workflow_id;
    		$nodeDB->type = $node->name;
    		$nodeDB->index = $node->id;
    		if($node->value) $nodeDB->value = $node->value;
    		if($node->operator) $nodeDB->operator = $node->operator;
    		
    		if (!$nodeDB->save()) {
    			BpmWorkflow::findOne(['id' => $db_workflow_id])->delete();
    			Yii::trace($nodeDB->getErrors());
    			$response = array('error' => Yii::t("bpm", 'Error. Not saved.'));
    			echo json_encode($response);
    			return;
    		}
    	}
    	 
    	//Save wires
    	foreach($this->nodes as $node){
    		$nodeDB = BpmNode::findOne(['workflow_id' => $db_workflow_id, 'index' => $node->id]);
    		if(isset($node->adjacency[0])) $nodeDB->output_yes = BpmNode::findOne(['workflow_id' => $db_workflow_id, 'index' => $node->adjacency[0]])->id;
    		if(isset($node->adjacency[1])) $nodeDB->output_no = BpmNode::findOne(['workflow_id' => $db_workflow_id, 'index' => $node->adjacency[1]])->id;

    			if (!$nodeDB->save()) {
    				BpmWorkflow::findOne(['id' => $db_workflow_id])->delete();
    				$response = array('error' => Yii::t("bpm", 'Error. Not saved.'));
    				echo json_encode($response);
    				return;
    			}
    	}
    	
    	$response = array('error' => null);
    	echo json_encode($response);
    }
    
}