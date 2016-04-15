<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;

use meican\aaa\RbacController;
use meican\circuits\models\Reservation;
use meican\topology\models\Domain;
use meican\topology\models\Port;
use meican\base\models\Cron;
use meican\circuits\models\CircuitsPreference;
use meican\circuits\models\AutomatedTest;
use meican\circuits\forms\AutomatedTestForm;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class AutomatedTestController extends RbacController {

    public $enableCsrfValidation = false;

    public function actionIndex($mode = "read") {
        $data = new ActiveDataProvider([
            'query' => AutomatedTest::find()->where(['type'=> Reservation::TYPE_TEST]),
            'pagination' => [
                  'pageSize' => 15,
                ],
            'sort' => false,
        ]);

        return $this->render('/at/status', array(
            'data' => $data,
            'mode' => $mode,
            'domains' => json_encode(Domain::find()->orderBy(['name'=> "SORT ASC"])->asArray()->select(['id','name'])->all()),
        ));
    }
    
    public function actionCreate() {
        if(Yii::$app->request->isAjax) {
            $form = new AutomatedTestForm;
            if ($form->load($_POST)) {
                if ($form->validate() && $form->save()) {
                    $this->checkRequesterUrl();
                    Yii::$app->getSession()->addFlash("success", Yii::t("circuits", "Automated Test added successfully"));
                    return true;
                }
            }
             
            return false;
        } else {
            return $this->redirect(["index",'mode'=>'create']);
        }
    }

    private function checkRequesterUrl() {
        $pref = CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_MEICAN_REQUESTER_URL);
        if ($pref) {
            $url = Url::toRoute("/circuits/requester", "http");
            if ($pref->value != $url) {
                $pref->value = $url;
                $pref->save();
            }
        }
    }
    
    public function actionUpdate($id) {
        $form = new AutomatedTestForm;
        if ($form->load($_POST)) {
            $test = AutomatedTest::findOne($id);
                
            //Confere se usuário tem permissão para editar teste na origem OU no destino
             $source = $test->getSourceDomain()->one();
             $destination = $test->getDestinationDomain()->one();
             $permission = false;
             if($source && RbacController::can('test/delete', $source->name)) $permission = true;
             if($destination && RbacController::can('test/delete', $destination->name)) $permission = true;
            if(!$permission){
                Yii::$app->getSession()->addFlash("warning", Yii::t("circuits", "You are not allowed to update a automated test involving these selected domains"));
                return false;
            }
            
            $cron = Cron::findOneTestTask($id);
            $cron->freq = $form->cron_value;
            $cron->status = Cron::STATUS_PROCESSING;
            if ($cron->save()) {
                Yii::$app->getSession()->addFlash("success", Yii::t("circuits", "Automated Test updated successfully"));
                return true;
            }
        }
            
        return false;
    }
    
    public function actionDelete() {
        if(isset($_POST["ids"])) {
            foreach (json_decode($_POST["ids"]) as $testId) {
                $test = AutomatedTest::findOne($testId);
                
                //Confere se usuário tem permissão para remover teste na origem OU no destino
                 $source = $test->getSourceDomain()->one();
                 $destination = $test->getDestinationDomain()->one();
                 $permission = false;
                 if($source && RbacController::can('test/delete', $source->name)) $permission = true;
                 if($destination && RbacController::can('test/delete', $destination->name)) $permission = true;
                 
                 if(!$permission){
                     Yii::$app->getSession()->addFlash("warning", Yii::t("circuits", "You are not allowed to delete automated tests involving these selected domains"));
                    return true;
                 }
                 
                if(!$test->delete()) {
                    Yii::$app->getSession()->addFlash("error", Yii::t("circuits", "Error deleting Automated Test"));
                    return true;
                } else {
                    Yii::$app->getSession()->addFlash("success", Yii::t("circuits", "Automated Test deleted successfully"));
                }
            }
            
            return true;
        }
        
        return false;
    }
}
