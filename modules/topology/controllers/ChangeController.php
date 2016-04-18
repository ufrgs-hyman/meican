<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use yii\data\ActiveDataProvider;
use Yii;

use meican\aaa\RbacController;
use meican\topology\services\DiscoveryService;
use meican\topology\models\Change;
use meican\topology\models\DiscoveryTask;

/**
 * @author Maurício Quatrin Guerreiro
 */
class ChangeController extends RbacController {

    public function actionApplied($eventId=null) {
    	if(!self::can("synchronizer/read")){
    		return $this->goHome();
    	}
    	
        $searchModel = new TopologyChange;
        $dataProvider = $searchModel->searchApplied(Yii::$app->request->get(), $eventId);

        return $this->render('applied', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel]);
    }  

    public function actionApplyAll($task) {
        $task = DiscoveryTask::findOne($task);
        try {
            $task->applyChanges();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function actionApply($id) {
        $change = Change::findOne($id);
        return $change->apply();
    }
}
