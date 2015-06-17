<?php

namespace app\modules\circuits\models;

use yii\base\Model;
use app\models\Reservation;
use Yii;

class AuthorizationForm extends Model {
	
	public $id;
	public $name;
	public $bandwidth;
	public $request_user_id;
	public $domain_id;
	public $domain_name;
	public $type;
	
	public function __construct($reservation, $domain){
		$this->id = $reservation->id;
		$this->name = $reservation->name;
		$this->type = $reservation->type;
		$this->bandwidth = $reservation->bandwidth;
		$this->request_user_id = $reservation->request_user_id;
		$this->domain_id = $domain->id;
		$this->domain_name = $domain->name;
	}
	
}