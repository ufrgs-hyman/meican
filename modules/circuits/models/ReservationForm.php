<?php

namespace app\modules\circuits\models;

use yii\base\Model;
use app\models\Aggregator;
use app\models\Reservation;
use app\models\ReservationRecurrence;
use app\models\Port;
use app\models\Device;
use app\models\ReservationPath;
use app\models\Connection;
use app\models\Preference;

use Yii;
use app\components\DateUtils;

class ReservationForm extends Model {
	
	//request reservation
	public $src_domain;
	public $src_port; 
	public $src_vlan;
	public $dst_domain;
	public $dst_port; 
	public $dst_vlan;
	public $name;
	public $start_time;
	public $start_date;
	public $finish_time;
	public $finish_date;
	public $bandwidth;
	public $waypoints;
	public $pro_enabled;
	public $gri;
	
	//reservation recurrence
	public $rec_enabled;
	public $rec_type;
	public $rec_interval;
	public $rec_weekdays;
	public $rec_finish_type;
	public $rec_finish_date;
	public $rec_finish_occur_limit;
	
	public $request;
	public $reservation;
	
	public function rules()	{
		return [
			[['src_domain','src_port','dst_domain','dst_port', 'name', 'start_time','start_date', 
				'finish_time','finish_date', 'bandwidth'], 'required'],
			[['rec_enabled','rec_type', 'rec_interval', 'rec_weekdays', 'rec_finish_type', 'rec_finish_date', 
				'rec_finish_occur_limit', 'waypoints', 'src_vlan', 'dst_vlan'], 'safe'],
		];
	}
	
	public function save() {
 			$this->reservation = new Reservation;
 			$this->reservation->type = Reservation::TYPE_NORMAL;
 			$this->reservation->name = $this->name;
 			$this->reservation->date = DateUtils::now();
 			$this->reservation->start = DateUtils::toUTC($this->start_date, $this->start_time);
 			$this->reservation->finish = DateUtils::toUTC($this->finish_date, $this->finish_time);
 			$this->reservation->bandwidth = $this->bandwidth;
 			$this->reservation->requester_nsa = CircuitsPreference::findOneValue(CircuitsPreference::MEICAN_NSA);
 			$this->reservation->provider_nsa = CircuitsPreference::findOneValue(CircuitsPreference::CIRCUITS_DEFAULT_PROVIDER_NSA);
 			$this->reservation->request_user_id = Yii::$app->user->getId(); 			
 			
 			if ($this->reservation->save()) {
 				if ($this->rec_enabled) {
 					$recurrence = new ReservationRecurrence;
 					$recurrence->type = $this->rec_type;
 					$recurrence->every = $this->rec_interval;
 					$this->rec_weekdays ? $recurrence->weekdays = implode(',', $this->rec_weekdays) : null;
 					$this->rec_finish_date ? $recurrence->finish = DateUtils::toUTC($this->rec_finish_date, "23:59") : null;
 					$this->rec_finish_occur_limit ? $recurrence->occurrence_limit = $this->rec_finish_occur_limit : null;
 					$recurrence->id = $this->reservation->id;
 					
 					if (!$recurrence->save()) {
 						Yii::trace($recurrence->getErrors());
 					}
 				}
 				
 				$path = new ReservationPath;
 				$path->reservation_id = $this->reservation->id;
 				$path->port_urn = Port::find()->where(['id' => $this->src_port])->one()->urn;
 				$path->path_order = 0;
 				$path->vlan = $this->src_vlan;
 				
 				if (!$path->save()) {
 					Yii::trace($path->getErrors());
 				}
 				
 				$waySize = 0;
 				if ($this->waypoints) {
 					$waySize = count($this->waypoints['port']);
 					for ($i = 0; $i < $waySize; $i++) {
 						$path = new ReservationPath;
 						$path->reservation_id = $this->reservation->id;
 						$path->path_order = $i + 1;
 						$port = Port::findOne($this->waypoints['port'][$i]);
 						$path->port_urn = $port->urn;
 						$path->vlan = $this->waypoints['vlan'][$i];
 						
 						if (!$path->save()) {
 							Yii::trace($path->getErrors());
 						}
 					}
 				}
 				
 				$path = new ReservationPath; 
 				$path->reservation_id = $this->reservation->id;
 				$path->port_urn = Port::find()->where(['id' => $this->dst_port])->one()->urn;
 				$path->path_order = $waySize + 1;
 				$path->vlan = $this->dst_vlan;

 				if ($path->save()) {
 					$this->reservation->createConnections();
 				} else {
 					Yii::trace($path->getErrors());
 				}
 			}
 			
 			Yii::trace($this->reservation->getErrors());
 		
 		return true;
	}
}