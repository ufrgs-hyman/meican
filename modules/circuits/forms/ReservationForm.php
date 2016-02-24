<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

use meican\circuits\models\Reservation;
use meican\circuits\models\ReservationRecurrence;
use meican\topology\models\Port;
use meican\topology\models\Device;
use meican\circuits\models\ReservationPath;
use meican\circuits\models\Connection;
use meican\circuits\models\CircuitsPreference;
use meican\base\components\DateUtils;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ReservationForm extends Model {
	
	//request reservation
	public $path;
	public $name;
	public $gri;
	public $bandwidth;
	public $protection;
    public $date_range;
	
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
			[['name', 'bandwidth', 'path', 'protection', 'date_range'], 'required'],
			[['rec_enabled','rec_type', 'rec_interval', 'rec_weekdays', 'rec_finish_type', 'rec_finish_date', 
				'rec_finish_occur_limit', 'gri'], 'safe'],
		];
	}
	
	public function save() {
 			$this->reservation = new Reservation;
 			$this->reservation->type = Reservation::TYPE_NORMAL;
 			$this->reservation->gri = trim($this->gri) == "" ? null : str_replace(" ", "", $this->gri);
 			$this->reservation->name = $this->name;
 			$this->reservation->protected = $this->protection ? 1 : 0;
 			$this->reservation->date = DateUtils::now();
            $this->date_range = explode(" - ", $this->date_range);
            $this->date_range[0] = explode(" ", $this->date_range[0]);
            $this->date_range[1] = explode(" ", $this->date_range[1]);
 			$this->reservation->start = DateUtils::toUTC($this->date_range[0][0], $this->date_range[0][1]);
 			$this->reservation->finish = DateUtils::toUTC($this->date_range[1][0], $this->date_range[1][1]);
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

                for ($i=0; $i < count($this->path['port']); $i++) { 
                    $path = new ReservationPath;
                    $path->reservation_id = $this->reservation->id;
                    $path->port_urn = Port::find()->where(['id' => $this->path['port'][$i]])->one()->urn;
                    $path->path_order = $i;
                    $path->vlan = $this->path['vlan'][$i];
                    
                    if (!$path->save()) {
                        Yii::trace($path->getErrors());
                    }
                }
 			}
 			
 			Yii::trace($this->reservation->getErrors());
 		
 		return true;
	}
}