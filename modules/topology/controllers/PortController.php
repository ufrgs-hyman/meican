<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\controllers;

use Yii;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;

use meican\topology\models\Device;
use meican\topology\models\Network;
use meican\topology\models\Domain;
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
	
		if($port->load($_POST)) {
			$port->type = 'NSI';
			$port->directionality = 'BI';
			if (!$port->validate()){
				return $this->renderPartial('_add-port',array(
					'networks' => $domain->getNetworks(),
					'devices' => $domain->getDevices(),
					'port' => $port,
				));
			}
			$port->save();
			return;
		}
		
		return $this->renderPartial('_add-port',array(
				'networks' => $domain->getNetworks(),
				'devices' => $domain->getDevices(),
				'port' => $port,
		));
	}
	
	public function actionUpdate($id){
		$port = Port::findOne($id);
		$domain = $port->getDevice()->one()->getDomain()->one();
		
		if($port->load($_POST)) {
			$port->type = 'NSI';
			$port->directionality = 'BI';
			if (!$port->validate()){
				return $this->renderPartial('_edit-port',array(
						'networks' => $domain->getNetworks(),
						'devices' => $domain->getDevices(),
						'port' => $port,
				));
			}
			$port->save();
			return;
		}
		
		return $this->renderPartial('_edit-port',array(
				'networks' => $domain->getNetworks(),
				'devices' => $domain->getDevices(),
				'port' => $port,
		));
	}
	
	public function actionDelete(){
		if(isset($_POST['delete'])){
			foreach ($_POST['delete'] as $id) {
			$port = Port::findOne(['id' => $id]);
				if(self::can('domainTopology/delete', $port->getDevice()->one()->getDomain()->one()->name)){
					$domain_id = $port->getDevice()->one()->domain_id;
					$port->delete();
				}
				else return Yii::t('topology', 'Ports were not deleted. You are not allowed to delete on domain {domain}', ['domain' => $port->getDevice()->one()->getDomain()->one()->name]);
			}
			return true;
		}
		return false;
	}
	
	public function actionGetDomainId($id){
		$port = Port::findOne($id);
		return $port->getDevice()->one()->getDomain()->one()->id;
	}
}
