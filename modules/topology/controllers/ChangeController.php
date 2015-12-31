<?php

namespace meican\topology\controllers;

use yii\data\ActiveDataProvider;
use Yii;

use meican\aaa\RbacController;
use meican\topology\models\TopologySynchronizer;
use meican\topology\models\TopologyChange;
use meican\topology\models\TopologySyncEvent;


class ChangeController extends RbacController {

    public function actionPending($eventId) {
    	if(!self::can("synchronizer/read")){
    		return $this->goHome();
    	}
    	
        $searchModel = new TopologyChange;
        $dataProvider = $searchModel->searchPending(Yii::$app->request->get(), $eventId);

        return $this->render('pending', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'eventId' => $eventId]);
    }   

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

    public function actionApplyAll($eventId) {
        $event = TopologySyncEvent::findOne($eventId);
        try {
            $event->applyChanges();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function actionApply($id) {
        $change = TopologyChange::findOne($id);
        return $change->apply();
    }
}
