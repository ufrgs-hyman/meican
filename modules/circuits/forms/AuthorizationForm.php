<?php

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

class AuthorizationForm extends Model {
	
	public $id;
	public $name;
	public $bandwidth;
	public $request_user_id;
	public $domain;
	public $source;
	public $destination;
	public $type;
	
	/*public function __construct($reservation, $domain){
		$this->id = $reservation->id;
		$this->name = $reservation->name;
		$this->type = $reservation->type;
		$this->bandwidth = $reservation->bandwidth;
		$this->request_user_id = $reservation->request_user_id;
		$this->domain = $domain->name;
	}*/
	
	public function setValues($reservation, $domain, $source, $destination){
		$this->id = $reservation->id;
		$this->name = $reservation->name;
		$this->type = $reservation->type;
		$this->bandwidth = $reservation->bandwidth;
		$this->request_user_id = $reservation->request_user_id;
		$this->domain = $domain;
		$this->source = $source;
		$this->destination = $destination;
	}
	
}