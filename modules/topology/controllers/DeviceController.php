<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use yii\data\ActiveDataProvider;

use app\models\Device;
use app\models\Port;

use app\modules\topology\models\DeviceSearch;

use yii\helpers\Json;
use Yii;

class DeviceController extends RbacController {
	
    public function actionIndex($id = null) {
    	self::canRedir("topology/read");
    	
        $searchModel = new DeviceSearch;
	    $allowedDomains = self::whichDomainsCan('topology/read');
	    $dataProvider = $searchModel->searchTerminatedByDomains(Yii::$app->request->get(),
	    		$allowedDomains);

        return $this->render('index', array(
        		'devices' => $dataProvider,
        		'searchModel' => $searchModel,
        		'allowedDomains' => $allowedDomains,
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
    	if(!self::can("topology/update", $device->domain_id)){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed for update on domain {domain}', ['domain' => $device->getDomain()->one()->name]));
    		return $this->redirect(array('index'));
    	}

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
    			if(self::can('topology/delete', $device->domain_id)){
	    			if ($device->delete())	Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Device {name} deleted', ['name'=>$device->name]));
	    			else Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting device {name}', ['name'=>$device->name]));
    			}
    			else Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Device {device} not deleted. You are not allowed for delete on domain {domain}', ['device' => $device->name, 'domain' => $device->getDomain()->one()->name]));
    		}
    	}
    
    	return $this->redirect(array('index'));
    }
    
    //REST
    
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
        $query = Device::findBySql("
            SELECT * 
            FROM `meican_device` AS dev 
            WHERE dev.id IN ( 
                SELECT port.device_id 
                FROM `meican_port` AS port 
                WHERE port.network_id = :network)")->addParams([':network'=>$id])->orderBy(['name'=>'SORT ASC'])->asArray();
        
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
    
    public function actionGetParentLocation($id) {
        $temp = Json::encode(Device::findOneParentLocation($id));
        Yii::trace($temp);
        return $temp;
    }
}
