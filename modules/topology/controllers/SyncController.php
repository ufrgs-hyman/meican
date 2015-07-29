<?php

namespace app\modules\topology\controllers;

use yii\data\ActiveDataProvider;

use app\controllers\RbacController;
use app\models\TopologySynchronizer;
use app\models\TopologyChange;
use app\models\Service;

use Yii;

class SyncController extends RbacController {
    
    public function actionIndex() {
        self::canRedir("topology/read");
        
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
            $sync->enabled = 1;
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
        $sync = TopologySynchronizer::findOne($id);
        $sync->execute();
        
        return true;
    }

    public function actionCreate(){
        self::canRedir("topology/create");
        
        $sync = new TopologySynchronizer;
        
        if($sync->load($_POST)) {
            $sync->enabled = true;
            if ($sync->save()) {
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
        self::canRedir("topology/update");

        $sync = TopologySynchronizer::findOne($id);
        
        if($sync->load($_POST)) {
            if ($sync->save()) {
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
        self::canRedir("topology/delete");
        
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
