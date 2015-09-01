<?php

namespace app\modules\topology\controllers;

use yii\data\ActiveDataProvider;

use app\controllers\RbacController;
use app\models\TopologySynchronizer;
use app\modules\topology\models\SyncForm;
use app\modules\topology\models\NSIParser;
use app\models\TopologyChange;
use app\models\Service;

use Yii;

class SyncController extends RbacController {
    
    public function actionIndex() {
    	if(!self::can("synchronizer/read")){ //Se ele não tiver permissão em nenhum domínio
			return $this->goHome();
		}
        
        $dataProvider = new ActiveDataProvider([
                'query' => TopologySynchronizer::find(),
                'pagination' => false,
                'sort' => false,
        ]);
        
        return $this->render('index', array(
                'items' => $dataProvider,
        ));
    }

    public function actionAddService($id) {
        $service = Service::findOne($id);

        if ($service) {
            $sync = new TopologySynchronizer;
            $sync->url = $service->url;
            $sync->type = $service->type;
            $sync->name = $service->getType();
            $sync->auto_apply = 0;

            if ($sync->save()) {
                Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Service {name} added successfully", ['name'=>$sync->name]));
            } else {
                Yii::$app->getSession()->addFlash("error", Yii::t("topology", "Service {name} was not added", ['name'=>$sync->name]));
            }
        }

        return $this->redirect(array('index'));
    }

    public function actionExecute($id) { 
    	if(!self::can("synchronizer/read")){ //Se ele não tiver permissão em nenhum domínio
    		return $this->goHome();
    	}
    	
    	$sync = TopologySynchronizer::findOne($id);
        $sync->execute();
        
        return $sync->syncEvent ? $sync->syncEvent->id : false;
    }

    public function actionCreate(){
    	if(!self::can("synchronizer/create")){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to add synchronizers'));
    		return $this->redirect(array('index'));
    	}
        
        $sync = new SyncForm;
        
        if($sync->load($_POST)) {
            if ($sync->save()) {
                $sync->saveCron();
                Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Synchronizer instance {name} added successfully", ['name'=>$sync->name]));
                return $this->redirect(array('index'));
            } else {
                foreach($sync->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
                $sync->clearErrors();
            }
        }
        
        return $this->render('create',[
                'model' => $sync,
        ]);
    }
    
    public function actionUpdate($id){
    	if(!self::can("synchronizer/update")){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to update synchronizers'));
    		return $this->redirect(array('index'));
    	}

        $sync = SyncForm::build($id);
        
        if($sync->load($_POST)) {
            if ($sync->save()) {
                $sync->saveCron();
                Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Synchronizer instance {name} updated successfully", ['name'=>$sync->name]));
                return $this->redirect(array('index'));
            } else {
                foreach($sync->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
                $sync->clearErrors();
            }
        }
        
        return $this->render('update',[
                'model' => $sync,
        ]);
    }
    
    public function actionDelete() {
    	if(!self::can("synchronizer/delete")){
    		Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to delete synchronizers'));
    		return $this->redirect(array('index'));
    	}
        
        if(isset($_POST['delete'])){
            foreach ($_POST['delete'] as $id) {
                $sync = TopologySynchronizer::findOne($id);
                if ($sync->delete()) {
                    Yii::$app->getSession()->addFlash('success', Yii::t("topology", "Synchronizer instance {name} deleted successfully", ['name'=>$sync->name]));
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Error deleting Synchronizer instance '.$sync->name);
                }
            }
        }
    
        return $this->redirect(array('index'));
    }
}
