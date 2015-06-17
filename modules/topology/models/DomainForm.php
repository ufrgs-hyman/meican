<?php

namespace app\modules\topology\models;

use app\models\Urn;
use app\models\Device;
use app\models\Network;
use app\models\Domain;
use Yii;
use app\modules\topology\models\UrnForm;


class DomainForm{
	
	public $id;
	private $descr;
	private $topo_id;
	private $networks;
	private $devices;
	private $urns;
	
	public function setInfo($domId){
		$d = Domain::find()->where(['id' => $domId])->one();

		if($d){
			$this->id = $d->id;
			$this->descr = $d->name;
			$this->topo_id = $d->topology;
			
			$this->networks = Network::find()->where(['domain_id' => $this->id])->all();
			if($this->networks){
				$devicesAux = Device::find()->where(['network_id' => $this->networks[0]->id]);
				foreach ($this->networks as $net):
					$devicesAux->union(Device::find()->where(['network_id' => $net->id]));
				endforeach;
				$this->devices = $devicesAux->all();
				$this->urns = Array();
				if($this->devices) $this->setURNs();
			}
		}
	}
	
	public function setURNs(){
		foreach ($this->devices as $dev):
			$netName = Network::find()->where(['id' => $dev->network_id])->one()->name;
			$urnsQuery = $dev->getUrns()->all();
			foreach ($urnsQuery as $u):
				$urn = new UrnForm();
				$urn->id = $u->id;
				$urn->value = $u->value;
				$urn->port = $u->port;
				$urn->max_capacity = $u->max_capacity;
				$urn->min_capacity = $u->min_capacity;
				$urn->granularity = $u->granularity;
				$urn->device_id = $u->device_id;
				$urn->device = $dev->name;
				$urn->network = $netName;
				
				$vlans ="";
				$vlansArray = $u->getVlanRanges()->all();
				$len = count($vlansArray);
				$c = 0;
				foreach ($vlansArray as $vlan):
					$c++;
					$vlans .= $vlan->value;
					if($c < $len) $vlans .= ",";
				endforeach;
				$urn->vlans = $vlans;

				$this->urns[] = $urn;
			endforeach;
		endforeach;
		
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getDescr(){
		return $this->descr;
	}
	
	public function getUrl(){
		return $this->idc_url;
	}
	
	public function getTopoId(){
		return $this->topo_id;
	}
	
	public function getNetworks(){
		return $this->networks;
	}
	
	public function getDevices(){
		return $this->devices;
	}
	
	public function getUrns(){
		return $this->urns;
	}

}
