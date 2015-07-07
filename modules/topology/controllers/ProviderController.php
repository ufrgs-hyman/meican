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
	
	public function actionGetByDomains($domains) {
		$doms = json_decode($domains);
		
		$providers = Provider::find()->where(["type"=>Provider::TYPE_AGGREGATOR])->asArray()->all();
		
		$temp = Json::encode($providers);
    	Yii::trace($temp);
    	return $temp;
	}

	public function actionIndex() {
    	$dataProvider = new ActiveDataProvider([
    			'query' => Provider::find()->orderBy('name'),
    			'pagination' => false,
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
    			'pagination' => false,
    			'sort' => false,
    	]);
    	
        return $this->render('view', array(
        		'model' => $prov,
        		'services' => $dataProvider
        ));
    }
}
