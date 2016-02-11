<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\controllers;

use yii\data\ActiveDataProvider;

use meican\aaa\RbacController;
use meican\topology\models\Domain;
use meican\topology\models\Port;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ViewerController extends RbacController {
	
    public function actionIndex() {
    	if(!self::can("domainTopology/read") && !self::can("domain/read")){
    		return $this->goHome();
    	}
    	
        return $this->render('index', ['domains'=>Domain::find()->select(['id','name'])->asArray()->all()]);
    }

    //REST functions
    
    public function actionGetNetworkLinks() {
    	$urns = Port::find()->where(['not', ['alias_id' => null]])->andWhere(
            ['not',['network_id'=> null]])->select(['id','network_id','alias_id'])->all();
    	$sdpNets = [];
    	foreach ($urns as $urn) {
    		$netId1 = $urn->network_id;
    		$netId2 = $urn->getAlias()->select(
                ['network_id'])->one()->network_id;
    		if ($netId1 <= $netId2) {
    			$sdpNets[$netId1."to".$netId2] = [$netId1, $netId2];
    		} else {
    			$sdpNets[$netId2."to".$netId1] = [$netId2, $netId1];
    		}
    	}
    	Yii::trace($sdpNets);
    	return json_encode($sdpNets);
    }

    public function actionGetDeviceLinks() {
        $portsWithAlias = Port::find()->where(['not', ['alias_id' => null]])->select(['id','device_id','alias_id'])->all();
        $deviceLinks = [];
        foreach ($portsWithAlias as $port) {
            $devId1 = $port->device_id;
            $devId2 = $port->getAlias()->select(['device_id'])->one()->device_id;
            if ($devId1 <= $devId2) {
                $deviceLinks[$devId1."to".$devId2] = [$devId1, $devId2];
            } else {
                $deviceLinks[$devId2."to".$devId1] = [$devId2, $devId1];
            }
        }
        Yii::trace($deviceLinks);
        return json_encode($deviceLinks);
    }

    public function actionSearch($term) {
        $term = str_replace("urn:ogf:network:", "", $term);
        $ports = Port::findBySql(
            "SELECT `name`, `device_id`, `network_id` 
            FROM `meican_port` 
            WHERE ((`urn` COLLATE UTF8_GENERAL_CI LIKE :term) 
            OR (`name` LIKE :term)) AND `directionality` = 'BI' AND `type` = 'NSI'
            LIMIT 5")->addParams([':term'=>'%'.$term.'%'])->asArray()->all();
        Yii::trace($ports);
        return json_encode($ports);
    }
}
