<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\controllers\RbacController;

use app\models\Port;

use Yii;

class ViewerController extends RbacController {
	
    public function actionIndex() {
    	self::canRedir("topology/read");
    	
        return $this->render('index');
    }

    //REST functions
    
    public function actionGetSdps() {
    	$urns = Port::find()->where(['not', ['alias_urn_id' => null]])->select(['id','device_id','alias_urn_id'])->all();
    	$sdpNets = [];
    	foreach ($urns as $urn) {
    		$netId1 = $urn->getDevice()->select(['network_id'])->one()->network_id;
    		$netId2 = $urn->getAlias()->select(['device_id'])->one()->getDevice()->select(['network_id'])->one()->network_id;
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
}
