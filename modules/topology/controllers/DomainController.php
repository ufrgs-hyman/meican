<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Yii;
use yii\helpers\Json;

use app\models\Network;
use app\models\Domain;
use app\models\Provider;
use app\modules\topology\models\DomainFormModel;
use app\models\Urn;
use app\models\Aggregator;

use app\models\BpmWorkflow;

class DomainController extends RbacController {
	
    public function actionIndex() {
    	self::canRedir("topology/read");

    	$domains = self::whichDomainsCan("topology/read");

    	$domainsWithIds = [];
    	foreach($domains as $domain){
    		$domainsWithIds[$domain->id] = $domain;
    	}

    	$dataProvider = new ArrayDataProvider([
    			'allModels' => $domainsWithIds,
    			'sort' => false,
    			'pagination' => false,
    	]);
    	
        return $this->render('index', array(
        		'domains' => $dataProvider,
        ));
    }

    public function actionCreate(){
    	self::canRedir("topology/create");
    	
    	$form = new DomainFormModel;
    	
    	if($form->load($_POST)) {
    		if ($form->validate()) {
				$dom = $form->getDomain();
				 
				if (!$dom->save()) {
					foreach($dom->getErrors() as $attribute => $error) {
						Yii::$app->getSession()->addFlash("error", $error[0]);
					}
				} else {
					Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Domain {name} added successfully', ['name'=>$dom->name]));
					return $this->redirect(array('index'));
				}
			}
    	}
    	
    	return $this->render('create',[
    			'domain' => $form,
    	]);
    }
    
    public function actionUpdate($id) {
    	if(!self::can("topology/update", $id)){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed for update the domain {domain}', ['domain' => Domain::findOne($id)->name]));
    		return $this->redirect(array('index'));
    	}
    	
    	$form = new DomainFormModel; 
    	$form->setFromRecord(Domain::findOne($id));
    	
    	if($form->load($_POST)) {
    		if ($form->validate()) {
    			$provider = $form->getProvider();
    			
    			if (!$provider->save()) {
    				foreach($provider->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    			} else {
    				$dom = $form->getDomain();
    				
    				if (!$dom->save()) {
    					foreach($dom->getErrors() as $attribute => $error) {
    						Yii::$app->getSession()->addFlash("error", $error[0]);
    					}
    				} else {
    					Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Domain {name} updated successfully', ['name'=>$dom->name]));
    					return $this->redirect(array('index'));
    				}
    			}
    		} else {
    			foreach($form->getErrors() as $attribute => $error) {
    				Yii::$app->getSession()->addFlash("error", $error[0]);
    			}
    			$form->clearErrors();
    		}
    	} 

    	return $this->render('update',[
    			'domain' => $form,
    	]);
    } 

    public function actionDelete() {
    	self::canRedir("topology/delete");
    	
    	if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $domainId) {
    			$dom = Domain::findOne($domainId);
    			if(self::can("topology/delete", $domainId)){
	    			if ($dom->delete()) {
	    				Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Domain {name} deleted', ['name'=>$dom->name]));
	    			} else {
	    				Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting domain {name}', ['name'=>$dom->name]));
	    			}
    			}
    			else Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed for delete on domain {domain}', ['domain' => $dom->name]));
    		}
    	}
    
    	return $this->redirect(array('index'));
    }
    
    //REST

    public function actionGetAll($cols=null){
    	$cols ? $data = Domain::find()->orderBy(['name'=> "SORT ASC"])->asArray()->select(json_decode($cols))->all() : $data = Domain::find()->orderBy(['name'=> "SORT ASC"])->asArray()->all();
    
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
    
    public function actionGetByNetwork($id){
    	$net = Network::findOne($id);
		$data = $net->getDomain()->toArray(['id', 'name']);
    
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
    
    public function actionGetByUrn($id, $cols=null) {
    	$urn = Urn::findOne($id);
    	$cols ? $data = $urn->getDevice()->one()->
    		getNetwork()->one()->getDomain()->select($cols)->one() : $data = $urn->getDevice()->one()->
    			getNetwork()->one()->getDomain()->one();
    	
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
}
