<?php
namespace app\modules\bpm\models;

use Yii;

class GraphNode{
	
	/*
	 * Toda classe foi implementado sobre um conceito diferente, de que existiria
	 * paralelismo no workflow e sem o conceito de IF ELSE nos nodos. Ela pode ser
	 * simplificada com base no novo conceito. Pode, por exemplo existir apenas duas
	 * adjacencias, SIM e NAO. Não existe mais necessidade de uma lista.
	 */

	public $id;
	public $name;
	public $adjacency =  [null,null];

	/* Type codes
	 * 0 - New_Request
	 * 1 - Domain
	 * 2 - User
	 * 3 - Bandwidth
	 * 4 - Request_User_Authorization
	 * 5 - Request_Group_Authorization
	 * 6 - Hour
	 * 7 - WeekDay
	 * 8 - Duration
	 * 9 - Group
	 * 20 - Accept_Automatically
	 * 30 - Deny_Automatically
	 */
	public $type;
	public $value = NULL;
	public $operator = NULL;
	
	public $entryConnected = false;
		
	public function addAdjacency($tgt, $way){
		$isNew = true;
		if($this->adjacency[$way])
			if($this->adjacency[$way] == $tgt) $isNew = false;
		if($isNew == true) $this->adjacency[$way] = $tgt;
	}
	
	public function isConnected(){
		if($this->entryConnected == false && $this->type != 0) return "notCon";
		
		if($this->type != 20 && $this->type != 30 && $this->type != 0){
			$conYes = false;
			$conNo = false;
			if(isset($this->adjacency[0])) $conYes = true;
			if(isset($this->adjacency[1])) $conNo = true;

			if($conYes && $conNo){
				if($this->type != 0){
					if($this->adjacency[0] == $this->adjacency[1]) return "repeated";
				}
				return "ok";
			}
			else return "notCon";
		}
		
		if($this->type == 0){
			if(!isset($this->adjacency[0])) return "notCon";
			else return "ok";
		}
		
		return "ok"; //Accept and Deny nodes
	}
	
	
	public function removeAdjacency($val){
		if($this->adjacency[0] == $val){
			$this->adjacency[0]=null;
			return 0;
		} 
		else{
			$this->adjacency[1]=null;
			return 1;
		} 
	}
	
	public function setEntryConnected(){
		$this->entryConnected = true;
	}
	
	public function setName($name){
		$this->name = $name;
		switch ($name) {
			case "New_Request":
				$this->type = 0;
				break;
			case "Domain":
				$this->type = 1;
				break;
			case "User":
				$this->type = 2;
				break;
			case "Bandwidth":
				$this->type = 3;
				break;
			case "Request_User_Authorization":
				$this->type = 4;
				break;
			case "Request_Group_Authorization":
				$this->type = 5;
				break;
			case "Hour":
				$this->type = 6;
				break;
			case "WeekDay":
				$this->type = 7;
				break;
			case "Duration":
				$this->type = 8;
				break;
			case "Group":
				$this->type = 9;
				break;
			case "Accept_Automatically":
				$this->type = 20;
				break;
			case "Deny_Automatically":
				$this->type = 30;
				break;
		}
	}
	
	public function getRealName(){
		switch($this->name){
			case 'New_Request':
				return "Arriving a new request";
				break;
			case 'Domain':
				return "Filter by domain";
				break;
			case 'User':
				return "Filter by requesting user";
				break;
			case 'Bandwidth':
				return "Filter by requested bandwidth";
				break;
			case 'Request_User_Authorization':
				return "Request authorization to single administrator";
				break;
			case 'Request_Group_Authorization':
				return "Request authorization to administrators group";
				break;
			case 'Accept_Automatically':
				return "Authorization Accepted";
				break;
			case 'Deny_Automatically':
				return "Authorization Denied";
				break;
			case 'Hour':
				return "Filter by schedule";
				break;
			case 'Group':
				return "Filter by group";
				break;
			case 'WeekDay':
				return "Filter by week day";
				break;
			case 'Duration':
				return "Filter by duration";
				break;
		}
	}

}