<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use yii\data\ActiveDataProvider;

use app\models\Urn;
use app\models\Device;
use app\models\Network;
use app\models\Domain;
use Yii;
use app\modules\topology\models\DomainForm;

use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\grid\ActionColumn;
use app\components\LinkColumn;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\data\ArrayDataProvider;
use yii\i18n\Formatter;

class stdClass {};

use yii\helpers\Json;

class UrnController extends RbacController {
	
    public function actionIndex($id = null){
    	self::canRedir("topology/read");
    	
    	//Pega os dominios que o usuário tem permissão
    	$domains = self::whichDomainsCan("topology/read");
    	
        return $this->render('index', array(
        		'domains' => $this->getDomains(),
        		'selected_domain' => $id,
        ));
    }

    public function getDomains(){
    	$allDomains = self::whichDomainsCan("topology/read");
    	if($allDomains){
    		$domains = array();
    		foreach ($allDomains as $d):
	    		$domain = new DomainForm();
	    		$domain->setInfo($d->id);
	    		$domains[] = $domain;
    		endforeach;
    		return $domains;
    	}
    	else return $allDomains;
    }
    
    public function actionCreate(){
    	$urn = new Urn;
    
    	if(isset($_POST['value'])) {
    		$permission = self::can('topology/create', Network::find()->where(['name' => $_POST['network']])->one()->domain_id);
    		
    		if($permission){
				$urn->value = $_POST['value'];
				$urn->port = $_POST['port'];
				$urn->max_capacity = $_POST['max_capacity'];
				$urn->min_capacity = $_POST['min_capacity'];
				$urn->granularity = $_POST['granularity'];
	
				$netId = Network::find()->where(['name' => $_POST['network']])->one()->id;
				$urn->device_id = Device::find()->where(['name' => $_POST['device']])->andWhere(['network_id' => $netId])->one()->id;			
	
				if ($urn->save()) {
					$urn->updateVlans($_POST['vlan']);
					echo "ok";
				}
				else{
					echo "error";
				}
    		}

    	}
    	else echo "error";
    	
    }
    
    public function actionUpdate(){
    	if(isset($_POST['value'])) {
    		$urn = Urn::find()->where(['id' => $_POST['id']])->one();
    		
    		$permission = self::can('topology/update', $urn->getDevice()->one()->getNetwork()->one()->domain_id);
    		
    		if($permission){
    
	    		$urn->value = $_POST['value'];
				$urn->port = $_POST['port'];
				$urn->max_capacity = $_POST['max_capacity'];
				$urn->min_capacity = $_POST['min_capacity'];
				$urn->granularity = $_POST['granularity'];
	
				$netId = Network::find()->where(['name' => $_POST['network']])->one()->id;
				$urn->device_id = Device::find()->where(['name' => $_POST['device']])->andWhere(['network_id' => $netId])->one()->id;			
	
				if ($urn->save()) {
					$urn->updateVlans($_POST['vlan']);
					$domain = $urn->getDevice()->one()->getNetwork()->one()->domain_id;
					echo $domain;
				}
				else{
					echo "error";
				}
    		}
    		else echo false;
		}
    }
        
    public function actionDelete(){
    	if(isset($_POST['id'])){
	    	$id = $_POST['id'];
			$urn = Urn::findOne(['id' => $_POST['id']]);
		 	if(self::can('topology/delete', $urn->getDevice()->one()->getNetwork()->one()->domain_id)){
		    	if(isset($_POST['show'])) Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Successful delete URN {urn} from domain {domain}', ['urn' => $urn->value, 'domain' => $urn->getDevice()->one()->getNetwork()->one()->getDomain()->one()->name]));
		 		$domain_id = $urn->getDevice()->one()->getNetwork()->one()->domain_id;
		 		$urn->delete();
		    	echo ($domain_id);
	    	}
	    	else{
	    		if(isset($_POST['show'])) Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'URN {urn} not deleted. You are not allowed for delete on domain {domain}', ['urn' => $urn->value, 'domain' => $urn->getDevice()->one()->getNetwork()->one()->getDomain()->one()->name]));
	    		echo false;
	    	}
    	}
    }
        
    public function actionGetDomainId(){
    	$urnId = $_POST['urnId'];
    	echo Urn::find()->where(['id' => $urnId])->one()->getDevice()->one()->getNetwork()->one()->getDomain()->select(['id'])->one()->id;
    }
    
    public function actionGetDomainName(){
    	echo json_encode(Domain::find()->where(['id' => $_GET['id']])->select(['name'])->one()->name);
    }
    
    public function actionGetDevicesNewRow(){
    	$networkName = $_GET['networkName'];
    	$domainId = $_GET['domainId'];

    	$arrayNetworks = Network::find()->where(['domain_id' => $domainId])->andWhere(['name' => $networkName])->all();

    	$array = array(Yii::t('topology', 'select'));
    	foreach ($arrayNetworks as $net):
	    	$sql = "SELECT name FROM meican_device WHERE network_id = ".$net->id;
    		$arrayDevices = Device::findBySql($sql)->all();
	    	foreach ($arrayDevices as $dev):
		    	$array[] = $dev->name;
	    	endforeach;
    	endforeach;

    	echo json_encode($array);
    }
    
    public function actionGetNetworksNewRow(){
    	$domId = $_GET['domainId'];
    	$arrayNetworks = Network::find()->where(['domain_id' => $domId])->all();
    	 
    	$array = array(Yii::t('topology', 'select'));
    	foreach ($arrayNetworks as $net):
    	$array[] = $net->name;
    	endforeach;
    
    	echo json_encode($array);
    }
    
    public function actionGetDomainsId(){
    	$domains = self::whichDomainsCan("topology/read");

    	foreach ($domains as $dom):
    		$array[] = $dom->id;
    	endforeach;
    	
    	echo json_encode($array);
    }
    
    public function actionGetUrn(){
    	$data = Urn::findOne($_GET['id']);
    	
    	$temp = Json::encode($data);
    	Yii::error($temp);
    	return $temp;
    }
    
    public function actionGetVlan(){
    	$urn = Urn::findOne($_GET['id']);
    	$data = $urn->getVlanRanges()->asArray()->all();
    
    	$temp = Json::encode($data);
    	Yii::error($temp);
    	return $temp;
    }
    
    public function actionGetByDevice($id, $cols=null){
    	$query = Urn::find()->where(['device_id'=>$id])->asArray();
    	
    	$cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();

    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
    
    public function actionGet($id, $cols=null) {
    	$cols ? $urn = Urn::find()->where(
    			['id'=>$id])->select($col)->asArray()->one() : $urn = Urn::find()->where(['id'=>$id])->asArray()->one();
    
    	$temp = Json::encode($urn);
    	Yii::trace($temp);
    	return $temp;
    }
    
	public function actionGetVlanRanges($urnId){
    	$urn = Urn::findOne($urnId);
    	$data = $urn->getVlanRanges()->asArray()->all();

    	$temp = Json::encode($data);
    	Yii::trace($temp);
    	return $temp;
    }
    
    public function actionCanUpdate(){
    	if(isset($_POST['id'])) {
    		$id = $_POST['id'];
			Urn::findOne(['id' => $id])->getDevice()->one()->getNetwork()->one()->domain_id;
    		echo self::can('topology/update', Urn::findOne(['id' => $id])->getDevice()->one()->getNetwork()->one()->domain_id);
    	}
    	else echo false;
    }
    
    public function actionCanCreate(){
    	if(isset($_POST['id'])) {
    		echo self::can('topology/create', $_POST['id']);
    	}
    	else echo false;
    }
    
    /**
     * The function receives a raw model array list and transforms into
     * a valid format for a Dropdown List. Set to TRUE the null option to have
     * a null selectable option in the menu
     * 
     * @param Array $modelList
     * @param String $key
     * @param String $value
     * @param Boolean $nullOption
     * @return Array $dropDown
     */
    protected function toDropDownFormat($modelList, $key, $value, $nullOption = FALSE){
    	$dropDown = [];
    
    	if($nullOption)
    		$dropDown[null] = null;
    	foreach($modelList as $item){
    		$dropDown[$item[$key]] = $item[$value];
    	}
    
    	return $dropDown;
    }	
}
