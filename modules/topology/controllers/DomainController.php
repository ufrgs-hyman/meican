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
    	if(!self::can("topology/read")){ //Se ele não tiver permissão em nenhum domínio
			return $this->goHome();
		}

    	$domains = self::whichDomainsCan("topology/read");

    	$dataProvider = new ArrayDataProvider([
    			'key'=>'id',
    			'allModels' => $domains,
    			'sort' => false,
    			'pagination' => [
                  'pageSize' => 20,
                ],
    	]);
    	
        return $this->render('index', array(
        		'domains' => $dataProvider,
        ));
    }

    public function actionCreate(){
    	$permission = self::can('topology/create');
		if(!$permission){
			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to add domains'));
    		return $this->redirect(array('index'));
	    }
    	
    	$dom = new Domain;
    	
    	if($dom->load($_POST)) {
    		if ($dom->validate()) {
				 
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
    			'domain' => $dom,
    	]);
    }
    
    public function actionUpdate($id) {
    	if(!self::can("topology/update", $id)){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to update the domain {domain}', ['domain' => Domain::findOne($id)->name]));
    		return $this->redirect(array('index'));
    	}
    	
    	$dom = Domain::findOne($id);
    	
    	if($dom->load($_POST)) {
    		if ($dom->validate()) {
    			if (!$dom->save()) {
    				foreach($dom->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    			} else {
    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Domain {name} updated successfully', ['name'=>$dom->name]));
    				return $this->redirect(array('index'));
    			}
    		} else {
    			foreach($form->getErrors() as $attribute => $error) {
    				Yii::$app->getSession()->addFlash("error", $error[0]);
    			}
    			$form->clearErrors();
    		}
    	} 

    	return $this->render('update',[
    			'domain' => $dom,
    	]);
    } 

    public function actionDelete() {    	
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
    			else Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to delete on domain {domain}', ['domain' => $dom->name]));
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

    public function actionGet($id, $cols=null){
        $query = Domain::find()->where(['id'=> $id])->asArray();

        $cols ? $data = $query->select(json_decode($cols))->one() : $data = $query->one();
    
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
}
