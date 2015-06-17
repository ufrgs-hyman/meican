<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;

use app\controllers\RbacController;

use app\models\Domain;
use app\models\Provider;
use app\models\Aggregator;

use app\modules\topology\models\AggregatorForm;

use Yii;

class AggregatorController extends RbacController
{
    public function actionIndex() {
    	self::canRedir("topology/read");
    	
    	$aggregators = Aggregator::find()->orderBy('id');
    	
    	$dataProvider = new ActiveDataProvider([
    			'query' => $aggregators,
    			'pagination' => false,
    			'sort' => false,
    	]);
    	
        return $this->render('index', array(
        		'aggregators' => $dataProvider,
        ));
    }
    
    public function actionSetDefault($id) {
    	self::canRedir("topology/update");
    	
    	if(Aggregator::resetDefault())  {
    		$agg = Aggregator::findOne($id);
    		$agg->default = 1;
    		if ($agg->save()) {
    			Yii::$app->getSession()->addFlash("success", Yii::t("topology", 'The new default provider is').": ".$agg->getProvider()->one()->nsa);
    		} else {
    			foreach($agg->getErrors() as $attribute => $error) {
    				Yii::$app->getSession()->addFlash("error", $error[0]);
    			}
    		}
    	} else Yii::$app->getSession()->addFlash("error", "Sorry, try again later.");
    	
    	$this->redirect("index");
    }
    
    public function actionCreate(){
    	self::canRedir("topology/create");
    	
    	$form = new AggregatorForm;
    	
    	if($form->load($_POST)) {
    		if ($form->validate()) {
    			$provider = $form->getProvider();
    			if ($provider->save()) {
    				$agg = new Aggregator;
    				$agg->default = null;
    				$agg->setProvider($provider);
    				if ($agg->save()) {
    					Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Aggregator {name} added successfully", ['name'=>$form->nsa]));
    					return $this->redirect(array('index'));
    				} else {
    					foreach($agg->getErrors() as $attribute => $error) {
    						Yii::$app->getSession()->addFlash("error", $error[0]);
    					}
    					
    					$provider->delete();
    				}
    			} else {
    				foreach($provider->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    			}
    		} else {
    				foreach($form->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    				$form->clearErrors();
    		}
    	}
    	
    	return $this->render('create',[
    			'aggregator' => $form,
    	]);
    }
    
    public function actionUpdate($id){
    	self::canRedir("topology/update");
    	
    	$agg = Aggregator::findOne($id);
    	$form = new AggregatorForm;
    	$form->setFromRecord($agg);
    	
    	if($form->load($_POST)) {
    		if($form->validate()) {
    			$prov = $form->getProvider();
    			if ($prov->save()) {
    				Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Aggregator {name} updated successfully", ['name'=>$form->nsa]));
    				return $this->redirect(array('index'));
    			} else {
    				foreach($prov->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
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
    			'aggregator' => $form,
    	]);
    }
    
	public function actionDelete() {
		self::canRedir("topology/delete");
		
    	if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $id) {
    			$agg = Aggregator::findOne($id);
    			$prov = $agg->getProvider()->one();
    			if ($agg->delete()) {
    				Yii::$app->getSession()->addFlash('success', Yii::t("topology", "Aggregator {name} deleted successfully", ['name'=>$prov->nsa]));
    			} else {
    				Yii::$app->getSession()->setFlash('error', 'Error deleting aggregator '.$agg->name);
    			}
    		}
    	}
    
    	return $this->redirect(array('index'));
    }
}
