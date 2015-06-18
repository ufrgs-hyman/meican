<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

use app\models\Network;
use app\models\Domain;
use app\models\Urn;
use yii\helpers\Json;
use Yii;
use yii\db\Query;

class NetworkController extends RbacController {
	
	public function actionIndex() {
		self::canRedir("topology/read");
		
		//Pega os dominios que o usuário tem permissão
    	$domains = self::whichDomainsCan("topology/read");

    	//Pega as redes destes dominios
	    $networks = Network::find()->where(['id' => '-1']);
	    foreach ($domains as $domain){
	    	$networks->union(Network::find()->where(['domain_id' => $domain->id]));
	    }
	    
	    $dataProvider = new ActiveDataProvider([
	    		'query' => $networks,
	    		'pagination' => false,
	    		'sort' => false,
	    ]);

        return $this->render('index', array(
        		'networks' => $dataProvider,
        ));
    }
    
    public function actionCreate(){
    	self::canRedir("topology/create");
    	
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
    			'domains' => self::whichDomainsCan('topology/create'),
    			'network' => $network,
    	]);
    }
    
    public function actionUpdate($id){
    	
    	$network = Network::findOne($id);
    	self::canRedir("topology/update", $network->domain_id);

    	if($network->load($_POST)) {
    			if ($network->save()) {
    				Yii::$app->getSession()->addFlash("success", "Network ".$network->name." updated successfully");
    				return $this->redirect(array('index'));
    			} else {
    				foreach($network->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    				$network->clearErrors();
    			}
    	}
    	
    	return $this->render('update',[
    			'domains' => Domain::find()->select(['id','name'])->all(),
    			'network' => $network,
    			]);
    }
    
    public function actionDelete(){
    	if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $id) {
    			$network = Network::findOne($id);
    			if(self::can("topology/delete", $network->domain_id)){
	    			if ($network->delete()) Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Network {name} deleted', ['name'=>$network->name]));
	    			else Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting network {name}', ['name'=>$network->name]));
    			}
    			else Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Network {net} not deleted. You are not allowed for delete on domain {domain}', ['net' => $network->name, 'domain' => $network->getDomain()->one()->name]));
    		}
    	}
    
    	return $this->redirect(array('index'));
    }
    
    //RESTfull
    
    public function actionGetAll(){
    	$data = Network::find()->asArray()->all();
    
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
    
    public function actionGetByUrn($id) {
    	$data = Urn::find()->where(['id'=>$id])->select(['id', 'device_id'])->one()->getDevice()->select([
    			'id','network_id'])->one()->getNetwork()->one();
    	
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
}
