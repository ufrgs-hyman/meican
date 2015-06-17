<?php

namespace app\modules\topology\controllers;

use yii\web\Controller;
use app\controllers\RbacController;
use Yii;
use app\models\DomainAggregator;
use app\models\Aggregator;
use app\models\Provider;
use app\models\Domain;
use app\models\Device;
use app\models\Urn;
use app\models\Network;
use app\modules\topology\models\ImportForm;
use app\modules\topology\models\AggregatorTopology;
use app\modules\topology\models\PerfsonarTopology;

class ImportController extends RbacController {
	
	public function actionIndex($id=null) {
		self::canRedir("topology/update");
		
		if (Yii::$app->params['aggregator.importer.enabled']) {
            return $this->actionAggregator($id);
		} elseif(Yii::$app->params['perfsonar.importer.enabled']) {
    		return $this->actionPerfsonar();
    	}
    	Yii::$app->getSession()->addFlash('error', Yii::t('topology', 'Importer is not enabled'));
    	return $this->goHome();
	}    
	
	public function actionResults() {
		return $this->render('results');
	}
	
	public function actionPerfsonar() {
		if(!Yii::$app->params['perfsonar.importer.enabled']) {
			Yii::$app->getSession()->addFlash('error', Yii::t('topology', 'perfSONAR importer is not enabled'));
			return $this->goHome();
		}
		
		$form = new ImportForm;
		$form->url = Yii::$app->params['perfsonar.default.url'];
		
		if($form->load($_POST)) {
			if($form->otherUrl != "") {
				$url = $form->otherUrl;
    		} else {
    			$url = $form->url;
    		}
    		
			$perfsonarTopo = new PerfsonarTopology($url);
			
			try {
				if($form->xml != "") {
					$perfsonarTopo->loadXml($form->xml);
				} else {
					$perfsonarTopo->loadFromDiscovery();
				}
				
				if (!$form->isUpdate()) {
					$this->deleteTopology();
				}
				
				$this->importTopology($perfsonarTopo);
			} catch (Exception $e) {
				Yii::$app->getSession()->addFlash('error', Yii::t('topology', 'Importing error').' '.$e->getMessage());
				return;
			}
		}
		
		return $this->render('perfsonar', array(
        		'model' => $form,
        ));
	}
    
    public function actionAggregator($id=null) {
    	$discoveryUrl = null;
    	if ($id) {
    		$provider = Provider::findOne($id);
    		if ($provider)
    			$discoveryUrl = $provider->discovery_url;
    	} 

    	if (!$discoveryUrl) {
    		$discoveryUrl = Yii::$app->params['aggregator.default.url'];
    	}
    	
		if(!Yii::$app->params['aggregator.importer.enabled']) {
			Yii::$app->getSession()->addFlash('error', Yii::t('topology', 'Aggregator importer is not enabled'));
			return $this->goHome();
		}
		
		$form = new ImportForm;
		$form->url = $discoveryUrl;
		
    	if($form->load($_POST)) {
    		if($form->otherUrl != "") {
				$url = $form->otherUrl;
    		} else {
    			$url = $form->url;
    		}
    		
    		$aggrTopo = new AggregatorTopology(
    				$url,
        			Yii::$app->params['meican.certificate.filename'],
        			Yii::$app->params['meican.certificate.passphrase']);
    
    		try {
	    		if($form->xml != "") {
	    			$aggrTopo->loadXml($form->xml);
	    		} else {
	    			$aggrTopo->loadFromDiscovery();
	    		}
	    		
	    		if (!$form->isUpdate()) {
	    			$this->deleteTopology();
	    		}
	    		
	    		$this->importTopology($aggrTopo);
	    		
	    		return $this->redirect('results');
    		} catch (Exception $e) {
    			Yii::$app->getSession()->addFlash('error', Yii::t('topology', 'Importing error').' '.$e->getMessage());
    			return;
    		}
    	}
    	
    	return $this->render('aggregator', array(
    			'model' => $form,
        ));
    }
    
    private function deleteTopology() {
    	foreach (Domain::find()->all() as $dom) {
    		$dom->delete();
    	}
    }
    
    //parsedTopology deve ser uma instancia de:
    //AggregatorTopology
    //ou
    //PerfsonarTopology
    private function importTopology($parsedTopology) {
    	foreach ($parsedTopology->getData()['domain'] as $domainName => $domainData) {
    		$domain = Domain::findOne(['topology'=>$domainName]);
    		
    		if ($domain == null) {
    			$domain = new Domain;
    			$domain->name = $domainName;
    			$domain->topology = $domainName;
    			$domain->oscars_version = Domain::getDefaultVersion();
    			$provider = new Provider();
		    	$provider->type = Domain::PROVIDER_TYPE;
		    	$provider->save();
		    	$domain->setProvider($provider);
    				
    			if (!$domain->save()) {
	    			foreach($domain->getErrors() as $attribute => $error) {
	    				Yii::$app->getSession()->addFlash("error", $error[0]);
	    			}
	    			
	    			continue;
    			} else {
    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Domain {topology} inserted', ['topology'=>$domain->topology]));    			}
    		} 
    		
    		if (isset($domainData['nsa'])) {
    			foreach ($domainData['nsa'] as $nsaId => $nsaData) {
    				$provider = Provider::find()->where(['nsa'=>$nsaId])->one();
    				if (!$provider) {
    					switch ($nsaData["type"]) {
    						case "AGGREGATOR" 	:
    							$provider = new Provider;
    							$provider->nsa = $nsaId;
    							$provider->type = $nsaData['type'];
    							$provider->connection_url = $nsaData['connection'];
    							$provider->discovery_url = $nsaData['discovery'];
    								
    							if (!$provider->save()) {
    								foreach($provider->getErrors() as $attribute => $error) {
    									Yii::$app->getSession()->addFlash("error", $error[0]);
    								}
    									
    								continue;
    							}
    							
    							$agg = new Aggregator;
    							$agg->setProvider($provider);
    							if($agg->save()) {
    								Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Aggregator {name} inserted', ['name'=>$provider->nsa]));  
    							} else {
    								foreach($agg->getErrors() as $attribute => $error) {
    									Yii::$app->getSession()->addFlash("error", $error[0]);
    								}
    							}
    							
    							break;
    						case "BRIDGE" 		:
    							$domainProvider = $domain->getProvider()->one();
    							if ($domainProvider->nsa) {
    								Yii::$app->getSession()->addFlash("error", Yii::t('topology', 'The domain {name} already has a Bridge. Another Bridge:', ['name'=>$domain->name]).' '.$nsaId);  
    							} else {
    								$domainProvider->nsa = $nsaId;
	    							$domainProvider->type = $nsaData['type'];
	    							$domainProvider->connection_url = $nsaData['connection'];
	    							$domainProvider->discovery_url = $nsaData['discovery'];
    								if ($domainProvider->save()) {
    									Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Bridge {name} updated', ['name'=>$domainProvider->nsa]));
    								} else {
    									foreach($domainProvider->getErrors() as $attribute => $error) {
    										Yii::$app->getSession()->addFlash("error", $error[0]);
    									}
    								}
    							}
    					}
    					
    					
    				}
    			}
    		}
    
    		$network = Network::findOne(['domain_id'=>$domain->id]);
    
    		if (!$network) {
    			$network = new Network;
    			$network->name = $domain->name;
    			$network->domain_id = $domain->id;
    			isset($domainData["latitude"]) ? $network->latitude = $domainData["latitude"] : $network->latitude = null;
    			isset($domainData["longitude"]) ? $network->longitude = $domainData["longitude"] : $network->longitude = null;
    				
    			if (!$network->save()) {
    				foreach($network->getErrors() as $attribute => $error) {
	    				Yii::$app->getSession()->addFlash("error", $error[0]);
	    			}
	    			
	    			continue;
    			} else {
    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Network {name} inserted', ['name'=>$network->name]));
    			}
    		}
    		
    		if (isset($domainData['devices'])) 
    			$this->importDevices($parsedTopology->getData()["sdp"], $domainData["devices"], $network);
    	}
    }
    
    private function importDevices($sdps, $devicesArray, $network) {
    	foreach ($devicesArray as $deviceName => $deviceData) {
    		$device = Device::findOne(['network_id'=>$network->id, 'node'=> $deviceName]);
    
    		if(!$device) {
    			$device = new Device;
    			$device->network_id = $network->id;
    			$device->name = $deviceName;
    			$device->ip = null;
    			$device->trademark = null;
    			$device->model = null;
    			$device->node = $deviceName;
    			$device->latitude = null;
    			$device->longitude = null;
    
    			if (!$device->save()) {
    				foreach($device->getErrors() as $attribute => $error) {
	    				Yii::$app->getSession()->addFlash("error", $error[0]);
	    			}
	    			continue;
    			} else {
    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Device {name} inserted', ['name'=>$device->name]));
    			}
    		} 
    		    
    		foreach ($deviceData["urns"] as $urnString => $urnData) {
    			$urn = Urn::findOne(['value'=>$urnString]);
    				
    			if(!$urn) {
    				$urn = new Urn;
    				$urn->value = $urnString;
    				isset($urnData["port"]) ? $urn->port = $urnData["port"] : $urn->port = null;
    				$urn->device_id = $device->id;
    				isset($urnData["capMax"]) ? $urn->max_capacity = $urnData["capMax"] : null;
    				isset($urnData["capMin"]) ? $urn->min_capacity = $urnData["capMin"] : null;
    				isset($urnData["granu"]) ? $urn->granularity = $urnData["granu"] : null;
    
    				if (!$urn->save()) {
	    				foreach($urn->getErrors() as $attribute => $error) {
		    				Yii::$app->getSession()->addFlash("error", $error[0]);
		    			}
	    			} else {
	    				isset($urnData["vlan"]) ? $urn->updateVlans($urnData["vlan"]) : null;
	    				
	    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'URN {name} inserted', ['name'=>$urn->value]));
	    			}
    			} else {
    				isset($urnData["capMax"]) ? $urn->max_capacity = $urnData["capMax"] : null;
    				isset($urnData["capMin"]) ? $urn->min_capacity = $urnData["capMin"] : null;
    				isset($urnData["granu"]) ? $urn->granularity = $urnData["granu"] : null;
    
    				if (!$urn->save()) {
    					foreach($urn->getErrors() as $attribute => $error) {
		    				Yii::$app->getSession()->addFlash("error", $error[0]);
		    			}
	    			} else {
	    				isset($urnData["vlan"]) ? $urn->updateVlans($urnData["vlan"]) : null;
	    				
	    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'URN {name} updated', ['name'=>$urn->value]));
	    			}
    			}
    			
    			if (isset($sdps[$urnData["portId"]])) {
    				$alias = Urn::findByValue($sdps[$urnData["portId"]])->one();
    				if ($alias) {
    					$urn->setAlias($alias);
    					$urn->save();
    					
    					if (!$urn->save()) {
    						foreach($urn->getErrors() as $attribute => $error) {
    							Yii::$app->getSession()->addFlash("error", $error[0]);
    						}
    					}
    				}
    			}
    		}
    	}
    }
}

?>
