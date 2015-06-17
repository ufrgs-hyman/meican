<?php

namespace app\modules\circuits\models;

use yii\base\Model;
use app\models\Reservation;
use app\models\Urn;
use app\models\ReservationPath;
use app\models\Connection;
use app\models\AutomatedTest;
use Yii;
use app\components\DateUtils;

class AutomatedTestForm extends Model {
	
	public $src_port;
	public $dst_port;
	public $freq_value;
	public $freq_type;
	public $provider;
	
	public $reservation;
	
	public function rules()	{
		return [
			[['src_port','dst_port','freq_type','freq_value','provider'], 'required'],
		];
	}
	
	public function save() {
 			$this->reservation = new Reservation;
 			$this->reservation->type = Reservation::TYPE_TEST;
 			$this->reservation->name = "MeicanAutomatedTest";
 			$this->reservation->date = DateUtils::now();
 			$this->reservation->start = DateUtils::now();
 			$this->reservation->finish =  DateUtils::now();
 			$this->reservation->bandwidth = 1;
 			$this->reservation->provider_id = $this->provider;
 			$this->reservation->request_user_id = Yii::$app->user->getId();
 			$this->reservation->path_confirmed = false;
 			
 			if ($this->reservation->save()) {
 				$this->reservation->name .= $this->reservation->id;
 				$this->reservation->save();
 				
 				$path = new ReservationPath;
 				$path->reservation_id = $this->reservation->id;
 				$srcUrn = Urn::findOne($this->src_port);
 				$path->domain_id = $srcUrn->getDevice()->one()->getNetwork()->one()->domain_id;
 				$path->src_urn_id = $this->src_port;
 				$path->path_order = 0;
 				$path->src_vlan = $srcUrn->getVlanRanges()->one()->value;
 				
 				if (!$path->save()) {
 					Yii::trace($path->getErrors());
 				}
 				
 				$path = new ReservationPath;
 				$path->reservation_id = $this->reservation->id;
 				$dstUrn = Urn::findOne($this->dst_port);
 				$path->domain_id = $dstUrn->getDevice()->one()->getNetwork()->one()->domain_id;
 				$path->dst_urn_id = $this->dst_port;
 				$path->path_order = 1;
 				$path->dst_vlan = $dstUrn->getVlanRanges()->one()->value;

 				if (!$path->save()) {
 					Yii::trace($path->getErrors());
 				}
 				
 				$test = new AutomatedTest;
 				$test->frequency_type = $this->freq_type;
 				$test->crontab_frequency = $this->freq_value;
 				$test->crontab_changed = 0;
 				$test->status = AutomatedTest::STATUS_PROCESSING;
 				$test->setReservation($this->reservation);
 				$test->save();
 				
 				Yii::trace($test->getErrors());
 			}
 			
 			Yii::trace($this->reservation->getErrors());
 		
 		return true;
	}
}

?>