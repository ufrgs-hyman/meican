<?php

namespace meican\modules\topology\controllers;

use yii\web\Controller;
use meican\controllers\RbacController;
use yii\data\ActiveDataProvider;

use meican\models\Device;
use meican\models\Network;
use meican\models\Domain;
use meican\models\Port;
use Yii;
use meican\modules\topology\models\DomainForm;

use yii\helpers\Json;

class PortController extends RbacController {
	
	public function actionIndex($id = null){
		if(!self::can("domainTopology/read")){
			return $this->goHome();
		}
		 
		return $this->render('index', array(
				'domains' =>self::whichDomainsCan("domainTopology/read"),
				'selected_domain' => $id,
		));
	}
	
	public function actionCreate(){
		$port = new Port;
	
		if(isset($_POST['name'])) {
			$domain = Network::find()->where(['name' => $_POST['network']])->one()->getDomain()->one();

			if(self::can('domainTopology/update', $domain->name)){
				$port->type = 'NSI';
				$port->directionality = 'BI';
				
				$port->name = $_POST['name'];
				$port->urn = $_POST['urn'];
				$port->max_capacity = $_POST['max_capacity'];
				$port->min_capacity = $_POST['min_capacity'];
				$port->granularity = $_POST['granularity'];
				$port->vlan_range = $_POST['vlan'];
	
				$port->network_id = Network::find()->where(['name' => $_POST['network']])->andWhere(['domain_id' => $domain->id])->one()->id;
				
				$port->device_id = Device::find()->where(['name' => $_POST['device']])->andWhere(['domain_id' => $domain->id])->one()->id;
	
				if ($port->save()) echo "ok";
				else echo "error";
			}
	
		}
		else echo "error";
		 
	}
	
	public function actionUpdate(){
		if(isset($_POST['name'])) {
			$port = Port::find()->where(['id' => $_POST['id']])->one();
	
			$domain = Network::find()->where(['name' => $_POST['network']])->one()->getDomain()->one();
	
			if(self::can('domainTopology/update', $domain->name)){
				$port->type = 'NSI';
				$port->directionality = 'BI';
				
				$port->name = $_POST['name'];
				$port->urn = $_POST['urn'];
				$port->max_capacity = $_POST['max_capacity'];
				$port->min_capacity = $_POST['min_capacity'];
				$port->granularity = $_POST['granularity'];
				$port->vlan_range = $_POST['vlan'];
	
				$port->network_id = Network::find()->where(['name' => $_POST['network']])->andWhere(['domain_id' => $domain->id])->one()->id;
				
				$port->device_id = Device::find()->where(['name' => $_POST['device']])->andWhere(['domain_id' => $domain->id])->one()->id;
	
				if ($port->save()) echo "ok";
				else echo "error";
			}
			else echo false;
		}
	}
	
	public function actionDeleteOne(){
		if(isset($_POST['id'])){
			$ids = $_POST['id'];
			$port = Port::findOne(['id' => $_POST['id']]);
			if(self::can('domainTopology/delete', $port->getDevice()->one()->getDomain()->one()->name)){
				$domain_id = $port->getDevice()->one()->domain_id;
				$port->delete();
				echo ($domain_id);
			}
			else echo false;
		}
	}
	
	public function actionDelete(){
		$ids = $_REQUEST['itens'];
		if(isset($_REQUEST['itens'])){
			$ids = $_REQUEST['itens'];
			foreach($ids as $id){
				$port = Port::findOne(['id' => $id]);
				if(self::can('domainTopology/delete', $port->getDevice()->one()->getDomain()->one()->name)){
					Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Successful delete port {port} from domain {domain}', ['port' => $port->name, 'domain' => $port->getDevice()->one()->getDomain()->one()->name]));
					$domain_id = $port->getDevice()->one()->domain_id;
					$port->delete();
				}
				else{
					Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Port {port} not deleted. You are not allowed to delete on domain {domain}', ['port' => $port->name, 'domain' => $port->getDevice()->one()->getDomain()->one()->name]));
				}
			}
		}
	}

	///REST
	
	public function actionGetDomainId(){
		$portId = $_POST['portId'];
		echo Port::find()->where(['id' => $portId])->one()->getDevice()->one()->getDomain()->select(['id'])->one()->id;
	}
	
	public function actionGetDomainName(){
		echo json_encode(Domain::find()->where(['id' => $_GET['id']])->select(['name'])->one()->name);
	}
	
	public function actionGetDomainsId(){
		$domains = self::whichDomainsCan("domainTopology/read");
	
		foreach ($domains as $dom):
		$array[] = $dom->id;
		endforeach;
		 
		echo json_encode($array);
	}
	
	public function actionGetPort(){
		$port = Port::findOne($_GET['id']);
		
		$data = [];
		$data['name'] = $port->name;
		$data['urn'] = $port->urn;
		$data['max_capacity'] = $port->max_capacity;
		$data['min_capacity'] = $port->min_capacity;
		$data['granularity'] = $port->granularity;
		
		return json_encode($data);
	}
	
	public function actionGetPortDevice(){
		$data = Port::findOne($_GET['id'])->getDevice()->one()->name;
		return json_encode($data);
	}
	
	public function actionGetPortNetwork(){
		$data = Port::findOne($_GET['id'])->getNetwork()->one()->name;
		return json_encode($data);
	}

	public function actionGetVlan(){
		$port = Port::findOne($_GET['id']);
		if($port->vlan_range) $data = $port->vlan_range;
		else $data = $port->getInboundPortVlanRange();
		
		$temp = Json::encode($data);
		
		return $temp;
	}
	
	public function actionCanUpdate(){
		if(isset($_POST['id'])) {
			$id = $_POST['id'];
			echo self::can('domainTopology/update', Port::findOne(['id' => $id])->getDevice()->one()->getDomain()->one()->name);
		}
		else echo false;
	}
	
	public function actionCanCreate(){
		if(isset($_POST['id'])) {
			echo self::can('domainTopology/create', Domain::findOne($_POST['id'])->name);
		}
		else echo false;
	}
	
	public function actionGetDevicesNew(){
		$domId = $_GET['domainId'];
	
		$arrayDevices = Device::find()->where(['domain_id' => $domId])->all();
	
		$array = array(Yii::t('topology', 'select'));
		foreach ($arrayDevices as $dev):
		$array[] = $dev->name;
		endforeach;
	
		echo json_encode($array);
	}
	
	public function actionGetNetworksNew(){
		$domId = $_GET['domainId'];
		$arrayNetworks = Network::find()->where(['domain_id' => $domId])->all();
	
		$array = array(Yii::t('topology', 'select'));
		foreach ($arrayNetworks as $net):
		$array[] = $net->name;
		endforeach;
	
		echo json_encode($array);
	}
    
    public function actionGetByDevice($id, $cols=null){
        $query = Port::find()->orderBy(['name'=> "SORT ASC"])->where(['device_id'=>$id])->asArray();
        
        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();

        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }

    public function actionGetAllBidirectional($cols=null) {
    	$query = Port::find()->where(['directionality'=> Port::DIR_BI])->asArray();
        
        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();

        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
    
    public function actionGet($id, $cols=null) {
        $cols ? $port = Port::find()->where(
                ['id'=>$id])->select($cols)->asArray()->one() : $port = Port::find()->where(['id'=>$id])->asArray()->one();
    
        $temp = Json::encode($port);
        Yii::trace($temp);
        return $temp;
    }
    
    public function actionGetVlanRange($id){
        $port = Port::find()->where(['id'=>$id])->select(['vlan_range','id'])->one();
        $data = $port->vlan_range;
        if(!$data) $data = $port->getInboundPortVlanRange();

        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }

    public function actionGetAllColor($cols=null) {
        $query = Port::find()->orderBy(['name'=>'SORT ASC'])->asArray();

        $cols ? $data = $query->select(array_merge(json_decode($cols), ['device_id']))->all() : $data = $query->all();

        foreach ($data as &$port) {
            $port['color'] = Domain::find()->where(['id'=>$dev['domain_id']])->select(['color'])->asArray()->one()['color'];
        }
        
        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
}
