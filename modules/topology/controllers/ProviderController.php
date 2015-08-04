<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\controllers\RbacController;

use app\models\Domain;
use app\models\Provider;
use yii\helpers\Json;
use Yii;

class ProviderController extends RbacController {
    
    public function actionCreate() {
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
        $model = Provider::findOne($id);

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
        $prov = Provider::findOne($id);

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
        self::canRedir("topology/delete");
        
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
