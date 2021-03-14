<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use Yii;

use meican\aaa\RbacController;
use meican\topology\models\Domain;
use meican\topology\models\Provider;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ProviderController extends RbacController {
    
    public function actionCreate() {
    	if(!self::can("domain/create")){
    		if(!self::can("domain/read")) {
                Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to access Topology Providers'));
                return $this->goHome();
            }
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to add providers'));
    			return $this->redirect(array('index'));
    		}
    	}
    	
    	$model = new Provider;

        if($model->load($_POST)) {
            if ($model->save()) {
                Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Provider {name} added successfully", ['name'=>$model->name]));
                return $this->redirect(array('view', 'id'=>$model->id));
            } else {
                foreach($model->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
                $model->clearErrors();
            }
        }

        return $this->render('create', array(
                'model' => $model,
        ));
    }

    public function actionUpdate($id) {
    	if(!self::can("domain/update")){
    		if(!self::can("domain/read")) return $this->goHome();
			else{
				Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to update providers'));
    			return $this->redirect(array('index'));
			}
    	}
    	
        $model = Provider::findOne($id);
        
        if(!isset($model)){
        	if(!self::can("domain/read")) return $this->goHome();
        	else{
        		Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Provider not found'));
        		return $this->redirect(array('index'));
        	}
        }

        if($model->load($_POST)) {
            if ($model->save()) {
                Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Provider {name} updated successfully", ['name'=>$model->name]));
                return $this->redirect(array('view', 'id'=>$model->id));
            } else {
                foreach($model->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
                $model->clearErrors();
            }
        }

        return $this->render('update', array(
                'model' => $model,
        ));
    }

    public function actionIndex() {
    	if(!self::can("domain/read")){
            Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to access Topology Providers'));
    		return $this->goHome();
    	}
    	
        $dataProvider = new ActiveDataProvider([
                'query' => Provider::find()->orderBy('name'),
                'pagination' => [
                  'pageSize' => 15,
                ],
                'sort' => false,
        ]);
        
        return $this->render('index', array(
                'providers' => $dataProvider,
        ));
    }

    public function actionView($id) {
    	if(!self::can("domain/read")){
    		return $this->goHome();
    	}
    	
        $prov = Provider::findOne($id);
        
        if(!isset($prov)){
        	if(!self::can("domain/read")) return $this->goHome();
        	else{
        		Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Provider not find'));
        		return $this->redirect(array('index'));
        	}
        }

        $dataProvider = new ActiveDataProvider([
                'query' => $prov->getServices(),
                'pagination' => [
                  'pageSize' => 10,
                ],
                'sort' => false,
        ]);
        
        return $this->render('view', array(
                'model' => $prov,
                'services' => $dataProvider
        ));
    }

    public function actionDelete() {
    	if(!self::can("domain/delete")){
    		if(!self::can("domain/read")) return $this->goHome();
			else{
				Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to delete providers'));
    			return $this->redirect(array('index'));
			}
    	}
        
        if(isset($_POST['delete'])){
            foreach ($_POST['delete'] as $id) {
                $prov = Provider::findOne($id);
                if ($prov->delete()) {
                    Yii::$app->getSession()->addFlash('success', Yii::t("topology", "Provider {name} deleted successfully", ['name'=>$prov->name]));
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Error deleting aggregator '.$prov->name);
                }
            }
        }
    
        return $this->redirect(array('index'));
    }

    //////////////////////

    public function actionGetByDomains($domains) {
        $doms = json_decode($domains);
        
        $temp = Json::encode($providers);
        Yii::trace($temp);
        return $temp;
    }

    public function actionGetAll($cols=null) {
        $query = Provider::find()->asArray()->orderBy(['nsa'=>'SORT ASC']);

        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();
        
        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
}
