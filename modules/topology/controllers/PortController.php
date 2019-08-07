<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use Yii;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;

use meican\topology\models\Network;
use meican\topology\models\Domain;
use meican\topology\models\Location;
use meican\topology\models\Port;
use meican\aaa\RbacController;

class PortController extends RbacController {
	
	public function actionIndex($id = null){
		if(!self::can("domainTopology/read")){
			return $this->goHome();
		}
		 
		return $this->render('index', array(
				'domains' =>self::whichDomainsCan("domainTopology/read"),
				'selected_domain' => $id,
		));
	}

	public function actionCreate($id){
		$port = new Port;
		$domain = Domain::findOne($id);
		$locations = Location::find()->where(['domain_id' => $id])->orderBy(['name'=>SORT_ASC]);

		if($port->load($_POST)) {
			$port->type = 'NSI';
			$port->directionality = 'BI';
			if (!$port->validate()){
				return $this->renderPartial('_add-port',array(
					'networks' => $domain->getNetworks(),
					'port' => $port,
					'locations' => $locations,
				));
			}
			$port->save();
			return;
		}
		
		return $this->renderPartial('_add-port',array(
				'networks' => $domain->getNetworks(),
				'port' => $port,
				'locations' => $locations,
		));
	}
	
	public function actionUpdate($id){
		$port = Port::findOne($id);
		$domain = $port->getNetwork()->one()->getDomain()->one();
		$locations = Location::find()->where(['domain_id' => $domain])->orderBy(['name'=>SORT_ASC]);
		
		if($port->load($_POST)) {
			$port->type = 'NSI';
			$port->directionality = 'BI';
			if (!$port->validate()){
				return $this->renderPartial('_edit-port',array(
						'networks' => $domain->getNetworks(),
						'port' => $port,
						'locations' => $locations,
				));
			}
			$port->save();
			return;
		}
		
		return $this->renderPartial('_edit-port',array(
				'networks' => $domain->getNetworks(),
				'port' => $port,
				'locations' => $locations,
		));
	}
	
	public function actionDelete(){
		if(isset($_POST['delete'])){
			foreach ($_POST['delete'] as $id) {
			$port = Port::findOne(['id' => $id]);
				if(self::can('domainTopology/delete', $port->getNetwork()->one()->getDomain()->one()->name)){
					$domain_id = $port->getNetwork()->one()->domain_id;
					$port->delete();
				}
				else return Yii::t('topology', 'Ports were not deleted. You are not allowed to delete on domain {domain}', ['domain' => $port->getNetwork()->one()->getDomain()->one()->name]);
			}
			return true;
		}
		return false;
	}
	
	public function actionGetDomainId($id){
		$port = Port::findOne($id);
		return $port->getDevice()->one()->getDomain()->one()->id;
	}

    public function actionGetByNetwork($id, $cols=null){
        $query = Port::find()->orderBy(['name'=> "SORT ASC"])
        	->where(['network_id'=>$id, 'directionality'=>'BI'])
        	->asArray();
        
        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();
        $temp = Json::encode($data);
        return $temp;
    }

	public function actionGetLocation($fields=null) {
		$query = Port::find()->innerJoin('meican_location', 'meican_location.id = meican_port.location_id')->select(['meican_location.name as location_name', 'meican_location.lat', 'meican_location.lng', 'network_id', 'location_id'])->asArray()->distinct(true);

        $fields ? $data = $query->select(explode(',',$fields))->all() : $data = $query->all();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
    }

    public function actionJson($fields=null, $dir=null, $type = 'NSI') {
		$port_fields = array(
			'meican_port.id',
			'type',
			'directionality',
			'urn', 
			'meican_port.name', 
			'capacity', 
			'max_capacity', 
			'min_capacity', 
			'vlan_range', 
			'biport_id', 
			'alias_id', 
			'network_id',
			'meican_location.name as location_name', 
			'meican_location.lat', 
			'meican_location.lng'
		);
    	$query = Port::find()->leftJoin('meican_location', 'meican_location.id = meican_port.location_id')->select($port_fields)->asArray()->orderBy(['name'=> "SORT ASC"]);
        $dir ? $data = ($type == 'ALL') ? $query->andWhere(['directionality'=> $dir]) : $query->andWhere(['directionality'=> $dir])->andWhere(['type'=> $type]) : null;
        $fields ? $data = $query->select(explode(',',$fields))->all() : $data = $query->all();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
    }

    public function actionGetAllBidirectional($cols=null) {
        $query = Port::find()->where(['directionality'=> Port::DIR_BI])->asArray();
        
        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();
        $temp = Json::encode($data);
        return $temp;
    }
    
    public function actionGet($id, $cols=null) {
        $cols ? $port = Port::find()->where(
                ['id'=>$id])->select($cols)->asArray()->one() : $port = Port::find()->where(['id'=>$id])->asArray()->one();
    
        $temp = Json::encode($port);
        return $temp;
    }
    
    public function actionGetVlanRange($id){
        $port = Port::find()->where(['id'=>$id])->select(['vlan_range','id'])->one();
        $data = $port->vlan_range;
        if(!$data) $data = $port->getInboundPortVlanRange();
        $temp = Json::encode($data);
        return $temp;
    }
    public function actionGetAllColor($cols=null) {
        $query = Port::find()->orderBy(['name'=>'SORT ASC'])->asArray();
        $cols ? $data = $query->select(array_merge(json_decode($cols), ['device_id']))->all() : $data = $query->all();
        foreach ($data as &$port) {
            $port['color'] = Domain::find()->where(['id'=>$dev['domain_id']])->select(['color'])->asArray()->one()['color'];
        }
        
        $temp = Json::encode($data);
        return $temp;
    }
}
