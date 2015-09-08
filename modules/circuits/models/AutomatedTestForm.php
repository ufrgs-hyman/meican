<?php

namespace app\modules\circuits\models;

use yii\base\Model;
use app\models\Reservation;
use app\models\Port;
use app\models\ReservationPath;
use app\models\Cron;
use Yii;
use app\components\DateUtils;

class AutomatedTestForm extends Model {
	
	public $src_port;
	public $src_vlan;
	public $dst_vlan;
	public $dst_port;
	public $cron_value;
	
	public $reservation;
	
	public function rules()	{
		return [
			[['src_port','src_vlan','dst_port','dst_vlan','cron_value'], 'required'],
		];
	}
	
	public function save() {
 			$this->reservation = new Reservation;
 			$this->reservation->type = Reservation::TYPE_TEST;
 			$this->reservation->name = AutomatedTest::AT_PREFIX;
 			$this->reservation->date = DateUtils::now();
 			$this->reservation->start = DateUtils::now();
 			$this->reservation->finish =  DateUtils::now();
 			$this->reservation->bandwidth = 1;
 			$this->reservation->requester_nsa = CircuitsPreference::findOneValue(CircuitsPreference::MEICAN_NSA);
 			$this->reservation->provider_nsa = CircuitsPreference::findOneValue(CircuitsPreference::CIRCUITS_DEFAULT_PROVIDER_NSA);
 			$this->reservation->request_user_id = Yii::$app->user->getId();
 			
 			if ($this->reservation->save()) {
 				$this->reservation->name .= $this->reservation->id;
 				$this->reservation->save();
 				
 				$path = new ReservationPath;
 				$path->reservation_id = $this->reservation->id;
 				$path->port_urn = Port::findOne($this->src_port)->urn;
 				$path->path_order = 0;
 				$path->vlan = $this->src_vlan;
 				
 				if (!$path->save()) {
 					Yii::trace($path->getErrors());
 					return false;
 				}

 				$path = new ReservationPath;
 				$path->reservation_id = $this->reservation->id;
 				$path->port_urn = Port::findOne($this->dst_port)->urn;
 				$path->path_order = 1;
 				$path->vlan = $this->dst_vlan;
 				
 				if (!$path->save()) {
 					Yii::trace($path->getErrors());
 					return false;
 				}

 				$cron = new Cron;
 				$cron->freq = $this->cron_value;
 				$cron->task_type = Cron::TYPE_TEST;
 				$cron->task_id = $this->reservation->id;
 				$cron->freq = $this->cron_value;
 				$cron->status = Cron::STATUS_PROCESSING;
 				if(!$cron->save()) {
 					Yii::trace($cron->getErrors());
 					return false;
 				}
 				
 			} else {
 				Yii::trace($this->reservation->getErrors());
 				return false;
 			}
 		
 		return true;
	}
}

?>