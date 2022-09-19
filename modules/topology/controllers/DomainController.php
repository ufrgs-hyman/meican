<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Yii;
use yii\helpers\Json;

use meican\aaa\RbacController;
use meican\topology\models\Network;
use meican\topology\models\Domain;
use meican\topology\models\Provider;
use meican\bpm\models\BpmWorkflow;

/**
 * @author Maurício Quatrin Guerreiro <@mqgmaster>
 */
class DomainController extends RbacController {
	
    public function actionIndex() {
    	if(!self::can("domain/read")){
			return $this->goHome();
		}
		
    	$domains = self::whichDomainsCan("domain/read");

    	$dataProvider = new ArrayDataProvider([
    			'key'=>'id',
    			'allModels' => $domains,
    			'sort' => false,
    			'pagination' => [
                  'pageSize' => 15,
                ],
    	]);
    	
        return $this->render('index', array(
        		'domains' => $dataProvider,
        ));
    }

    public function actionCreate(){
		if(!self::can('domain/create')){
			if(!self::can("domain/read")) return $this->goHome();
			else{
				Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to add domains'));
				return $this->redirect(array('index'));
			}
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
					return $this->redirect(array('create'));
				}
			}
    	}
    	
		$dom->grouped_nodes = true;
    	return $this->render('create',[
    			'domain' => $dom,
    	]);
    }
    
    public function actionUpdate($id) {
    	if(!self::can("domain/update")){
    		if(!self::can("domain/read")) return $this->goHome();
			else{
				Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to update domains'));
    			return $this->redirect(array('index'));
			}
    	}
    	
    	$dom = Domain::findOne($id);
    	
    	if(!isset($dom)){
    		if(!self::can("domain/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Domain not found'));
    			return $this->redirect(array('index'));
    		}
    	}
    	
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
    	if(self::can("domain/delete")){
	    	if(isset($_POST['delete'])){
	    		foreach ($_POST['delete'] as $domainId) {
	    			$dom = Domain::findOne($domainId);
	    			if ($dom->delete()) {
	    				Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Domain {name} deleted', ['name'=>$dom->name]));
	    			} else {
	    				Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting domain {name}', ['name'=>$dom->name]));
	    			}
	    		}
	    	}
    	}
    	else Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to delete domains'));
    
    	return $this->redirect(array('index'));
    }
    
    //REST

    public function actionGetAll($cols=null){
    	$cols ? $data = Domain::find()->orderBy(['name'=> "SORT ASC"])->asArray()->select(json_decode($cols))->all() : $data = Domain::find()->orderBy(['name'=> "SORT ASC"])->asArray()->all();
    
    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }

    public function actionGetLocation($cols=null){
        $cols ? $data = Domain::find()->orderBy(['id'=> "SORT ASC"])->asArray()->select(json_decode($cols))->all() : $data = Domain::find()->orderBy(['name'=> "SORT ASC"])->asArray()->all();
    
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

    public function actionGetByName($name, $cols= null) {
        $query = Domain::find()->where(['name'=> $name])->asArray();

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
