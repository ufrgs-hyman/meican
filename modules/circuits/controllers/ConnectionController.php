<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\controllers;

use yii\helpers\Url;
use Yii;

use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\Reservation;
use meican\topology\models\Port;
use meican\topology\models\Device;
use meican\topology\models\Domain;
use meican\topology\models\Provider;
use meican\aaa\RbacController;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ConnectionController extends RbacController {
	
	public function actionGetOrderedPathsOld($id) {
		$paths = ConnectionPath::find()->where(['conn_id'=>$id])->orderBy(['path_order'=> "SORT_ASC"])->all();
		 
		$data = [];
		 
		foreach ($paths as $path) {
			$port = $path->getPort()->select(['id','device_id'])->one();
			$data[] = [
				'path_order' => $path->path_order, 
				'device_id'=> $port ? $port->device_id : null
			];
		}
		 
		$data = json_encode($data);
		Yii::trace($data);
		return $data;
	}

    public function actionGetOrderedPaths($id) {
        $paths = ConnectionPath::find()->where(['conn_id'=>$id])->orderBy(['path_order'=> "SORT_ASC"])->all();
         
        $data = [];
         
        foreach ($paths as $path) {
            $port = $path->getPort()->select(['id','urn','device_id'])->one();
            $data[] = [
                'path_order' => $path->path_order, 
                'device_id'=> $port ? $port->device_id : null,
                'port_id' => $port ? $port->id : null,
                'port_urn' => $port ? $port->urn : null,
                'domain' => $path->domain
            ];
        }
         
        $data = json_encode($data);
        Yii::trace($data);
        return $data;
    }

	public function actionGetEndPoints($id) {
    	$conn = Connection::findOne($id);
    	if ($conn->external_id) {
    		$srcPath = $conn->getFirstPath()->one();
	    	$dstPath = $conn->getLastPath()->one();
    	} else {
    		$res = $conn->getReservation()->one();
    		$srcPath = $res->getFirstPath()->one();
	    	$dstPath = $res->getLastPath()->one();
    	}
    	$srcPort = $srcPath->getPort()->one();
    	$dstPort = $dstPath->getPort()->one();
    	$srcVlan = $srcPath->vlan;
    	$dstVlan = $dstPath->vlan;
    	$srcPortUrn = $srcPath->getFullPortUrn();
    	$dstPortUrn = $dstPath->getFullPortUrn();
    	
    	$source = null;
    	$dest = null;

    	$dev = $srcPort ? $srcPort->getDevice()->one() : null;
        $net = $srcPort ? $srcPort->getNetwork()->one() : null;
        $dom = $dev->getDomain()->one();
    	
    	$source["dom"] = $dom->name;
    	$source["net"] = $net ? $net->name: "";
    	$source["dev"] = $dev ? $dev->name: "";
    	$source["port"] = $srcPort ? $srcPort->name : "";
    	$source["vlan"] = $srcVlan;
    	$source["urn"] = $srcPortUrn;

    	$dev = $dstPort ? $dstPort->getDevice()->one() : null;
        $net = $dstPort ? $dstPort->getNetwork()->one() : null;
        $dom = $dev->getDomain()->one();
    	
    	$dest["dom"] = $dom->name;
    	$dest["net"] = $net ? $net->name: "";
    	$dest["dev"] = $dev ? $dev->name: "";
    	$dest["port"] = $dstPort ? $dstPort->name : "";
    	$dest["vlan"] = $dstVlan;
    	$dest["urn"] = $dstPortUrn;
    	
    	$data = json_encode(["src" => $source, "dst" => $dest]);
    	Yii::trace($data);
    	return $data;
    }
	
    public function actionGetStp($id) {
    	$dev = Device::findOneParentLocation($id);
        $dom = $dev->getDomain()->select(['name'])->one()->name;
    	
    	$data = [];
    	$data['id'] = $id;
    	$data["dom"] = $dom;
    	$data["name"] = $dev->name;
    	$data['lat'] = $dev->latitude;
    	$data['lng'] = $dev->longitude;
    	
    	$data = json_encode($data);
    	Yii::trace($data);
    	return $data;
    }

	public function actionCancel($id) {
		$conn = Connection::findOne($id);
    	$permission = false;
    	$reservation = Reservation::findOne(['id' => $conn->reservation_id]);
    	if(Yii::$app->user->getId() == $reservation->request_user_id) $permission = true; //Se é quem requisitou
    	else {
    		$domains_name = [];
    		foreach(self::whichDomainsCan('reservation/delete') as $domain) $domains_name[] = $domain->name;
    		
    		$paths = ConnectionPath::find()
		    		 ->where(['in', 'domain', $domains_name])
		    		 ->andWhere(['conn_id' => $conn->id])
		    		 ->select(["conn_id"])->distinct(true)->one();
    		
    		if(!empty($paths)) $permission = true;
    	}
		if($permission){
			$conn->requestCancel();
			return true;
		}
		else return false;
	}
}

?>