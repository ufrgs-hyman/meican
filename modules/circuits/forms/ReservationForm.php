<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

use meican\circuits\models\Reservation;
use meican\topology\models\Port;
use meican\topology\models\Device;
use meican\circuits\models\ReservationPath;
use meican\circuits\models\Connection;
use meican\circuits\models\CircuitsPreference;
use meican\base\utils\DateUtils;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ReservationForm extends Model {
	
	//request reservation
	public $path;
	public $name;
	public $bandwidth;
    public $events;
	
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
			[['name', 'bandwidth', 'path', 'events'], 'required'],
            [['bandwidth'], 'integer', 'min'=> 1],
		];
	}
	
	public function save() {
 			$this->reservation = new Reservation;
 			$this->reservation->type = Reservation::TYPE_NORMAL;
 			$this->reservation->name = $this->name;
 			$this->reservation->date = DateUtils::now();
 			$this->reservation->bandwidth = $this->bandwidth;
 			$this->reservation->requester_nsa = CircuitsPreference::findOneValue(CircuitsPreference::MEICAN_NSA);
 			$this->reservation->provider_nsa = CircuitsPreference::findOneValue(CircuitsPreference::CIRCUITS_DEFAULT_PROVIDER_NSA);
 			$this->reservation->request_user_id = Yii::$app->user->getId(); 			
 			
 			if ($this->reservation->save()) {
                for ($i=0; $i < count($this->path['port']); $i++) { 
                    $path = new ReservationPath;
                    $path->reservation_id = $this->reservation->id;
                    if($this->path['mode'][$i] == 'normal') 
                        $path->port_urn = Port::find()->where(['id' => $this->path['port'][$i]])->one()->urn;
                    else 
                        $path->port_urn = str_replace('urn:ogf:network:','',$this->path['urn'][$i]);
                    
                    $path->path_order = $i;
                    $path->vlan = $this->path['vlan'][$i];
                    
                    if (!$path->save()) {
                        Yii::trace($path->getErrors());
                    }
                }

                $this->reservation->createConnections($this->events);
 			}
 			
 			Yii::trace($this->reservation->getErrors());
 		
 		return true;
	}
}