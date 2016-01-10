<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\controllers;

use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use Yii;

use meican\aaa\RbacController;
use meican\topology\models\Device;
use meican\topology\models\Port;
use meican\topology\models\Domain;
use meican\topology\forms\DeviceSearch;


class DeviceController extends RbacController {
	
    public function actionIndex($id = null) {
    	if(!self::can("domainTopology/read")){
			return $this->goHome();
		}
    	
        $searchModel = new DeviceSearch;
	    $allowedDomains = self::whichDomainsCan('domainTopology/read');
	    $dataProvider = $searchModel->searchByDomains(Yii::$app->request->get(),
	    		$allowedDomains);

        return $this->render('index', array(
        		'devices' => $dataProvider,
        		'searchModel' => $searchModel,
        		'allowedDomains' => $allowedDomains,
        ));
    }
    
    public function actionCreate(){    	
    	if(!self::can('domainTopology/create')){
    		if(!self::can("domainTopology/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to add devices'));
    			return $this->redirect(array('index'));
    		}
    	}
    	
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
    			'domains' => self::whichDomainsCan('domainTopology/create'),
    	]);
    }
    
    public function actionUpdate($id){	
		$device = Device::findOne($id);
    	if(!isset($device)){
    		if(!self::can("domainTopology/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Device not found'));
    			return $this->redirect(array('index'));
    		}
    	}
    	if(!self::can("domainTopology/update", $device->getDomain()->one()->name)){
    		if(!self::can("domainTopology/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to update on domain {domain}', ['domain' => $device->getDomain()->one()->name]));
    			return $this->redirect(array('index'));
    		}
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
    	
    	return $this->render('update',[
    			'device' => $device,
    			'domains' => self::whichDomainsCan('domainTopology/update'),
    	]);
    }

    public function actionDelete(){
	    if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $id) {
    			$device = Device::findOne($id);
    			if(self::can('domainTopology/delete', $device->getDomain()->one()->name)){
	    			if ($device->delete())	Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Device {name} deleted', ['name'=>$device->name]));
	    			else Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting device {name}', ['name'=>$device->name]));
    			}
    			else Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Device {device} not deleted. You are not allowed to delete on domain {domain}', ['device' => $device->name, 'domain' => $device->getDomain()->one()->name]));
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
        //QUERIES DEPOIS DO FINDBYSQL SAO IGNORADAS PELO YII
        $query = Device::findBySql("
            SELECT id, name
            FROM `meican_device` AS dev 
            WHERE dev.id IN ( 
                SELECT port.device_id 
                FROM `meican_port` AS port 
                WHERE port.network_id = :network)
            ORDER BY `name` ASC")->addParams([':network'=>$id])->asArray();
        
        $data = $query->all();
    
        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }

    public function actionGetAll($cols=null) {
    	$query = Device::find()->orderBy(['name'=>'SORT ASC'])->asArray();

        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();
    	
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }

    public function actionGetAllColor($cols=null) {
        $query = Device::find()->orderBy(['name'=>'SORT ASC'])->asArray();

        $cols ? $data = $query->select(array_merge(json_decode($cols), ['domain_id']))->all() : $data = $query->all();

        foreach ($data as &$dev) {
            $dev['color'] = Domain::find()->where(['id'=>$dev['domain_id']])->select(['color'])->asArray()->one()['color'];
        }
        
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
