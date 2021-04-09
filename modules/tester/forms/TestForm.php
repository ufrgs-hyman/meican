<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\tester\forms;

use yii\base\Model;
use Yii;

use meican\circuits\models\CircuitsPreference;
use meican\topology\models\Port;
use meican\circuits\models\Reservation;
use meican\circuits\models\ReservationPath;
use meican\base\utils\DateUtils;
use meican\aaa\RbacController;
use meican\tester\models\Test;
use meican\scheduler\models\ScheduledTask;

/**
 * @author Maurício Quatrin Guerreiro
 */
class TestForm extends Model {
	
    public $src_dom;
    public $src_net;
	public $src_port;
	public $src_vlan;
    public $dst_dom;
    public $dst_net;
    public $dst_port;
	public $dst_vlan;
	public $cron_value;
	
	public $reservation;
	
	public function rules()	{
		return [
			[['src_dom','src_port','src_vlan',
            'dst_dom','dst_port','dst_vlan','cron_value'], 'required'],
            [['src_net', 'dst_net'], 'safe']
		];
	}

    public function attributeLabels() {
        return [
            'src_dom' => "Domain",
            'src_net' => "Network",
            'src_port' => "Port",
            'src_vlan' => "VLAN",
            'dst_dom' => "Domain",
            'dst_net' => "Network",
            'dst_port' => "Port",
            'dst_vlan' => "VLAN"
        ];
    }
	
	public function save() {
 			$this->reservation = new Reservation;
 			$this->reservation->type = Reservation::TYPE_TEST;
 			$this->reservation->name = Test::AT_PREFIX;
 			$this->reservation->date = DateUtils::now();
 			$this->reservation->start = DateUtils::now();
 			$this->reservation->finish =  DateUtils::now();
 			$this->reservation->bandwidth = 10;
 			$this->reservation->requester_nsa = CircuitsPreference::findOneValue(CircuitsPreference::MEICAN_NSA);
 			$this->reservation->provider_nsa = CircuitsPreference::findOneValue(CircuitsPreference::CIRCUITS_DEFAULT_PROVIDER_NSA);
 			$this->reservation->request_user_id = Yii::$app->user->getId();
 			
 			//Confere se usuário tem permissão para testar na origem OU no destino
 			$source = Port::findOne(['id' => $this->src_port]);
 			$destination = Port::findOne(['id' => $this->dst_port]);
 			$permission = false;
 			if($source){
 				$source = $source->getNetwork()->one();
 				if($source){
 					$domain = $source->getDomain()->one();
 					if($domain && RbacController::can('test/create', $domain->name)) $permission = true;
 				}
 			}
 			if($destination){
 				$destination = $destination->getNetwork()->one();
 				if($destination){
 					$domain = $destination->getDomain()->one();
 					if($domain && RbacController::can('test/create', $domain->name)) $permission = true;
 				}
 			}
 			if(!$permission){
 				Yii::$app->getSession()->addFlash('danger', Yii::t("circuits", "You are not allowed to create an automated test involving these selected domains"));
				return false;
 			}
 			
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

 				$task = new ScheduledTask;
 				$task->freq = $this->cron_value;
 				$task->obj_class = 'meican\tester\models\Test';
 				$task->obj_data = $this->reservation->id;
 				if(!$task->save()) {
 					Yii::trace($task->getErrors());
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