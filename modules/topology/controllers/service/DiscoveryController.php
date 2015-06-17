<?php

namespace app\modules\topology\controllers\service;

use yii\web\Controller;
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

class DiscoveryController extends Controller {
	
	public function actionIndex($id=null) {
		if (Yii::$app->params['aggIsEnabled']) {
            return $this->actionAggregator($id);
		} elseif(Yii::$app->params['perfsonarIsEnabled']) {
    		return $this->actionPerfsonar();
    	}
    	$this->setFlash("Importation is not enabled", "error");
    	return $this->redirect(array('/init'));
	}    
	
	public function actionNotification() {
		http_response_code(202);
		
		return "";
	}
	
	public function actionUnregister($id) {
		$r = new \HttpRequest('https://agg.cipo.rnp.br/dds/subscriptions/'.$id, \HttpRequest::METH_DELETE);
		$r->send();
		
		Yii::trace($r->getResponseCode()." ".$r->getResponseBody());
		return "";
	}
	
	public function actionRegister() {
		$r = new \HttpRequest('https://agg.cipo.rnp.br/dds/subscriptions', \HttpRequest::METH_POST);
		$r->addHeaders(array(
				'Accept-encoding' => 'application/xml;charset=utf-8',
				'Content-Type' => 'application/xml;charset=utf-8'));
		$r->setBody('<?xml version="1.0" encoding="UTF-8"?><tns:subscriptionRequest xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
			        xmlns:tns="http://schemas.ogf.org/nsi/2014/02/discovery/types">
			    <requesterId>urn:ogf:network:cipo.ufrgs.br:2014:nsa:meican</requesterId>
			    <callback>http://meican-cipo.inf.ufrgs.br/meican2/web/topology/service/discovery/notification</callback>
			    <filter>
			        <include>
			            <event>All</event>
			        </include>
			    </filter>
			</tns:subscriptionRequest>');
		
		$r->send();
		
		Yii::trace($r->getResponseCode()." ".$r->getResponseBody());
		return "";
	}
	
    public function actionAggregator($id=null) {
    	$discoveryUrl = null;
    	if ($id) {
    		$provider = Provider::findOne($id);
    		if ($provider)
    			$discoveryUrl = $provider->discovery_url;
    	} 

    	if (!$discoveryUrl) {
    		$discoveryUrl = Yii::$app->params['aggDiscoveryUrl'];
    	}
    	
		if(!Yii::$app->params['aggIsEnabled']) {
			Yii::$app->getSession()->addFlash('error', "Aggregator importation is not enabled");
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
        			Yii::$app->params['aggCertFileName'],
        			Yii::$app->params['aggCertPassword']);
    
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
    			Yii::$app->getSession()->addFlash('error', "Import error ".$e->getMessage());
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
    	$sourceAggregator = null;
    	if (isset($parsedTopology->sourceProvider['nsa']) && "AGGREGATOR" == $parsedTopology->sourceProvider['type']) {
    		$sourceProvider = Provider::find()->where(['nsa'=>$parsedTopology->sourceProvider['nsa']])->one();
    		if (!$sourceProvider) {
    			$sourceProvider = new Provider;
    			$sourceProvider->nsa = $parsedTopology->sourceProvider['nsa'];
    			$sourceProvider->type = $parsedTopology->sourceProvider['type'];
    			$sourceProvider->connection_url = $parsedTopology->sourceProvider['connection'];
    			$sourceProvider->discovery_url = $parsedTopology->sourceProvider['discovery'];
    			 
    			if (!$sourceProvider->save()) {
    				foreach($sourceProvider->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    				 
    				return;
    			}
    			 
    			$sourceAggregator = new Aggregator;
    			$sourceAggregator->setProvider($sourceProvider);
    			if(!$sourceAggregator->save()) {
    				foreach($sourceAggregator->getErrors() as $attribute => $error) {
    					Yii::$app->getSession()->addFlash("error", $error[0]);
    				}
    					
    				return;
    			}
    		} else {
    			$sourceAggregator = Aggregator::findOne($sourceProvider->id);
    		}
    	}
    	
    	foreach ($parsedTopology->getData() as $domainName => $domainData) {
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
    				Yii::$app->getSession()->addFlash("success", "Domain ".$domain->topology.
    					" inserted");
    			}
    		} 
    		
    		if ($sourceAggregator) {
    			$sourceAggregator->addDomain($domain);
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
    								Yii::$app->getSession()->addFlash("success", "Aggregator ".$provider->nsa.
    								" inserted");
    							} else {
    								foreach($agg->getErrors() as $attribute => $error) {
    									Yii::$app->getSession()->addFlash("error", $error[0]);
    								}
    							}
    							$agg->addDomain($domain);
    							
    							break;
    						case "BRIDGE" 		:
    							$domainProvider = $domain->getProvider()->one();
    							if ($domainProvider->nsa) {
    								Yii::$app->getSession()->addFlash("error", "The domain ".$domain->name." already has a Bridge. ".
    										"Only one bridge per domain is allowed. Another Bridge: ".$nsaId);
    							} else {
    								$domainProvider->nsa = $nsaId;
	    							$domainProvider->type = $nsaData['type'];
	    							$domainProvider->connection_url = $nsaData['connection'];
	    							$domainProvider->discovery_url = $nsaData['discovery'];
    								if ($domainProvider->save()) {
    									Yii::$app->getSession()->addFlash("success", "Bridge ".$domainProvider->nsa.
    									" updated");
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
    				Yii::$app->getSession()->addFlash("success", "Network ".$network->name.
    				" inserted");
    			}
    		}
    		
    		if (isset($domainData['devices'])) 
    			$this->importDevices($domainData["devices"], $network);
    	}
    }
    
    private function importDevices($devicesArray, $network) {
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
    				Yii::$app->getSession()->addFlash("success", "Device ".$device->name.
    				" inserted");
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
	    				
	    				Yii::$app->getSession()->addFlash("success", "URN ".$urn->value.
	    				" inserted");
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
	    				
	    				Yii::$app->getSession()->addFlash("success", "URN ".$urn->value.
	    				" updated");
	    			}
    			}
    		}
    	}
    }
}

?>
