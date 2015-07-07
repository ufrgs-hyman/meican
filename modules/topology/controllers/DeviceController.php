<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use yii\data\ActiveDataProvider;
use app\models\Device;
use app\models\Network;
use app\models\Domain;
use app\models\Port;
use yii\helpers\Json;
use Yii;

class DeviceController extends RbacController {
	
    public function actionIndex($id = null) {
    	self::canRedir("topology/read");
    	
        //Pega os dominios que o usuário tem permisão
        $domains = self::whichDomainsCan("topology/read");

        //Pega as redes destes dominios
        $networks = Network::find()->where(['id' => '-1']);
        foreach ($domains as $domain){
        	$networks->union(Network::find()->where(['domain_id' => $domain->id]));
        }

        return $this->render('index', array(
        		'networks' => $networks->all(),
        		'selected_network' => $id,
        ));
    }
    
    public function actionCreate(){
    	self::canRedir("topology/create");
    	
    	$device = new Device;
    	 
    	if($device->load($_POST)) {
    			if ($device->save()) {
    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Device {name} added successfully', ['name'=>$device->name]));
    					return $this->redirect(array('index'));
    			} else {
    					foreach($device->getErrors() as $attribute => $error) {
    						Yii::$app->getSession()->addFlash("error", $error[0]);
    					}
    					$device->clearErrors();
    			}
    	}

    	return $this->render('create',[
    			'device' => $device,
    			'domains' => self::whichDomainsCan('topology/create'),
    	]);
    }
    
    public function actionUpdate($id){
    	
		$device = Device::findOne($id);
    	self::canRedir('topology/update', $device->getNetwork()->one()->domain_id);

    	if($device->load($_POST)) {
    			if ($device->save()) {
    					Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Device {name} updated successfully', ['name'=>$device->name]));
    					return $this->redirect(array('index'));
    			} else {
    					foreach($device->getErrors() as $attribute => $error) {
    						Yii::$app->getSession()->addFlash("error", $error[0]);
    					}
    					$device->clearErrors();
    			}
    	}
    	$domains = self::whichDomainsCan('topology/update');
    	
    	return $this->render('update',[
    			'device' => $device,
    			'domains' => $domains,
    	]);
    }

    public function actionDelete(){
    	self::canRedir("topology/delete");
    	
	    if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $id) {
    			$device = Device::findOne($id);
    			if(self::can('topology/delete', $device->getNetwork()->one()->domain_id)){
	    			if ($device->delete())	Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Device {name} deleted', ['name'=>$device->name]));
	    			else Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting device {name}', ['name'=>$device->name]));
    			}
    			else Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Device {device} not deleted. You are not allowed for delete on domain {domain}', ['device' => $device->name, 'domain' => $device->getNetwork()->one()->getDomain()->one()->name]));
    		}
    	}
    
    	return $this->redirect(array('index'));
    }
    
    //REST
    
    public function actionGetNetworksByDomain(){
    	$domainName = $_GET['domainName'];
    	$domain = Domain::find()->where(['name' => $domainName])->one();
 
    	$networks = $domain->getNetworks()->all();
    	$temp = Json::encode($networks);
    	Yii::trace($temp);
    	return $temp;
    }
    
    public function actionGet($id) {
        $data = Device::find()->asArray()->where(['id'=>$id])->one();
    
        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
    
    public function actionGetByDomain($id, $cols=null){
    	$query = Device::find()->where(['domain_id' => $id])->orderBy(['name'=>'SORT ASC'])->asArray();
    	
    	$cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();
    
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }

    public function actionGetByNetwork($id, $cols=null){
        $ports = Port::find()->where(['network_id'=>$id])->select(['device_id'])->all();
        $devs =[];
        foreach ($ports as $port) {
            $devs[] = $port->device_id;
        }

        $query = Device::find()->where(['in','id',$devs])->orderBy(['name'=>'SORT ASC'])->asArray();
        
        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();
    
        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
    
    public function actionGetAll() {
    	$data = Device::find()->orderBy(['name'=>'SORT ASC'])->asArray()->select(['id','name','latitude','longitude','domain_id'])->all();
    	
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
    
    public function actionGetNetworksId() {
    	self::canRedir("topology/read");
    	 
    	//Pega os dominios que o usuário tem permisão
    	$domains = self::whichDomainsCan("topology/read");
    
    	//Pega as redes destes dominios
    	$networks = Network::find()->where(['domain_id' => $domains[0]->id]);
    	foreach ($domains as $domain){
    		$networks->union(Network::find()->where(['domain_id' => $domain->id]));
    	}
    	
    	$array = [];
    	foreach ($networks->all() as $net){
    		$array[] = $net->id;
    	}
    	
    	echo json_encode($array);
    }
    
    public function actionGetParentLocation($id) {
        $temp = Json::encode(Device::findOneParentLocation($id));
        Yii::trace($temp);
        return $temp;
    }
}
