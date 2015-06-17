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
		
	/*	$providers = [];
		if (count($doms) == 2 && $doms[0] == $doms[1]) {
			$provider = Provider::find()->where(['id'=>$doms[0]])->select(['id', 'nsa', 'type'])->one();
			$provider->nsa ? $providers[] = $provider : null;
			
			$aggs = Domain::findOne($doms[0])->getAggregators()->all();
			foreach ($aggs as $agg) {
				$prov = $agg->getProvider()->select(['id', 'nsa', 'type'])->one();
				$prov->type = "AGGREGATOR";
				$providers[] = $prov;
			}
		} else {
			foreach ($doms as $dom) {
				$aggs = Domain::findOne($dom)->getAggregators()->all();
				foreach ($aggs as $agg) {
					if(!$this->existsInArray($providers, $agg)) {
						$prov = $agg->getProvider()->select(['id', 'nsa', 'type'])->one();
						$prov->type = "AGGREGATOR";
						$providers[] = $prov;
					}
				}
			}
		}*/
		
		$providers = Provider::find()->where(["type"=>Provider::TYPE_AGGREGATOR])->asArray()->all();
		
		$temp = Json::encode($providers);
    	Yii::trace($temp);
    	return $temp;
	}
	
	private function existsInArray($array, $newItem) {
		foreach ($array as $item) {
			if ($item->id == $newItem->id) {
				return true;
			}
		}
		
		return false;
	}
}
