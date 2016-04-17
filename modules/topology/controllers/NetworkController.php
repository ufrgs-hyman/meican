<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use Yii;

use meican\aaa\RbacController;
use meican\topology\models\Network;
use meican\topology\models\Domain;
use meican\topology\models\Provider;
use meican\topology\forms\NetworkSearch;

/**
 * @author Maurício Quatrin Guerreiro <@mqgmaster>
 */
class NetworkController extends RbacController {
	
	public function actionIndex() {
		if(!self::can("domainTopology/read")){
			return $this->goHome();
		}
		
	    $searchModel = new NetworkSearch;
	    $allowedDomains = self::whichDomainsCan('domainTopology/read');
	    $dataProvider = $searchModel->searchByDomains(Yii::$app->request->get(),
	    		$allowedDomains);

        return $this->render('index', array(
        		'networks' => $dataProvider,
        		'searchModel' => $searchModel,
        		'allowedDomains' => $allowedDomains,
        ));
    }
    
    public function actionCreate(){
    	if(!self::can('domainTopology/create')){
    		if(!self::can("domainTopology/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to add networks'));
    			return $this->redirect(array('index'));
    		}
    	}
    	
    	$network = new Network;
    	 
    	if($network->load($_POST)) {
    			if ($network->save()) {
    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Network {name} added successfully', ['name'=>$network->name]));
    				return $this->redirect(array('index'));
    			} else {
    				foreach($network->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    				$network->clearErrors();
    			}
    	}
    	 
    	return $this->render('create',[
    			'domains' => self::whichDomainsCan('domainTopology/create'),
    			'network' => $network,
    	]);
    }
    
    public function actionUpdate($id){	
    	$network = Network::findOne($id);
    	if(!isset($network)){
    		if(!self::can("domainTopology/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Network not found'));
    			return $this->redirect(array('index'));
    		}
    	}
    	if(!self::can("domainTopology/update", $network->getDomain()->one()->name)){
    		if(!self::can("domainTopology/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to update on domain {domain}', ['domain' => $network->getDomain()->one()->name]));
    			return $this->redirect(array('index'));
    		}
    	}

    	if($network->load($_POST)) {
    			if ($network->save()) {
    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Network {name} updated successfully', ['name'=>$network->name]));
    				return $this->redirect(array('index'));
    			} else {
    				foreach($network->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    				$network->clearErrors();
    			}
    	}
    	
    	return $this->render('update',[
    			'domains' => self::whichDomainsCan('domainTopology/update'),
    			'network' => $network,
    			]);
    }
    
    public function actionDelete(){
    	if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $id) {
    			$network = Network::findOne($id);
    			if(self::can("domainTopology/delete", $network->getDomain()->one()->name)){
	    			if ($network->delete()) Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Network {name} deleted', ['name'=>$network->name]));
	    			else Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting network {name}', ['name'=>$network->name]));
    			}
    			else Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Network {net} not deleted. You are not allowed to delete on domain {domain}', ['net' => $network->name, 'domain' => $network->getDomain()->one()->name]));
    		}
    	}
    
    	return $this->redirect(array('index'));
    }
    
    //RESTfull
    
    public function actionGetAll($cols=null){
    	$query = Network::find()->orderBy(['name'=> "SORT ASC"])->asArray();

        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();
    
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }

    public function actionGetAllParentLocation($cols=null){
        $query = Network::find()->orderBy(['name'=> "SORT ASC"])->asArray();

        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();

        foreach ($data as &$net) {
            if ($net['latitude'] == null) {
                $prov = Provider::find()->where(['domain_id'=>$net['domain_id']])->select(['latitude','longitude'])->asArray()->one();
                $net['latitude'] = $prov['latitude'];
                $net['longitude'] = $prov['longitude'];
            }
        }
    
        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
    
    public function actionGetByDomain($id){
    	$data = Network::find()->where(['domain_id'=>$id])->asArray()->select(['id','name'])->all();
    	 
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
    
    public function actionGet($id) {
    	$data = Network::findOne($id);
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
    
    public function actionGetByPort($id) {
    	$data = Port::find()->where(['id'=>$id])->select(['id', 'device_id'])->one()->getDevice()->select([
    			'id','network_id'])->one()->getNetwork()->one();
    	
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
}
