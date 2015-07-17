<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\controllers\RbacController;

use app\models\Domain;
use app\models\Service;
use app\models\Provider;
use yii\helpers\Json;
use Yii;

class ServiceController extends RbacController {
    
    public function actionCreate($id) {
        $prov = Provider::findOne($id);
        if ($prov) {
            $model = new Service; 
            $model->provider_id = $prov->id;
        } else return $this->redirect(array('index'));

        if($model->load($_POST)) {
            if ($model->save()) {
                Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Service {type} added successfully", ['type'=>$model->getType()]));
                return $this->redirect(array('/topology/provider/view', 'id'=>$model->provider_id));
            } else {
                foreach($model->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
                $model->clearErrors();
            }
        }

        return $this->render('/provider/service/create', array(
                'model' => $model,
        ));
    }

    public function actionUpdate($id) {
        $model = Service::findOne($id);

        if($model->load($_POST)) {
            if ($model->save()) {
                Yii::$app->getSession()->addFlash("success", Yii::t("topology", "Service {type} updated successfully", ['type'=>$model->getType()]));
                return $this->redirect(array('/topology/provider/view', 'id'=>$model->provider_id));
            } else {
                foreach($model->getErrors() as $attribute => $error) {
                    Yii::$app->getSession()->addFlash("error", $error[0]);
                }
                $model->clearErrors();
            }
        }

        return $this->render('/provider/service/update', array(
                'model' => $model,
        ));
    }

    public function actionDelete() {
        if(isset($_POST['delete'])){
            foreach ($_POST['delete'] as $id) {
                $service = Service::findOne($id);
                if ($service->delete()) {
                    Yii::$app->getSession()->addFlash('success', Yii::t("topology", "Service {type} deleted successfully", ['type'=>$service->getType()]));
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Error deleting service '.$service->getType());
                }
            }
        }
    
        return $this->redirect(array('/topology/provider/view', 'id'=>$service->provider_id));
    }

    public function actionGetCsByProviderNsa($nsa, $cols=null) {
        $provider = Provider::findByNsa($nsa)->one();
        if (!$provider) {
            return [];
        }

        $query = $provider->getConnectionService()->asArray();

        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();
        
        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
}
