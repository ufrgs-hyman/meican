<?php
/**
 * @copyright Copyright (c) 2012-2019 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\controllers;

use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use Yii;

use meican\aaa\RbacController;
use meican\topology\models\Location;
use meican\topology\models\Port;
use meican\topology\models\Domain;
use meican\topology\forms\LocationSearch;

/**
 * @author Leonardo Lauryel Batista dos Santos <@leonardolauryel>
 */
class LocationController extends RbacController {
	
    public function actionIndex($id = null) {
      if(!self::can("domainTopology/read")){
		Yii::$app->getSession()->addFlash('danger', Yii::t('aaa', 'You are not allowed to access Topology Locations'));
		    return $this->goHome();
    }
    	
      $searchModel = new LocationSearch;
      $allowedDomains = self::whichDomainsCan('domainTopology/read');
      $dataProvider = $searchModel->searchByDomains(Yii::$app->request->get(),
      $allowedDomains);

      $domainsWithLocation = $searchModel->searchDomainsWithLocation($allowedDomains);

        return $this->render('index', array(
        		'locations' => $dataProvider,
        		'searchModel' => $searchModel,
        		'domainsWithLocation' => $domainsWithLocation,
        ));
    }
    
    public function actionCreate(){    	
    	if(!self::can('domainTopology/create')){
    		if(!self::can("domainTopology/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to add locations'));
    			return $this->redirect(array('index'));
    		}
    	}
    	
    	$location = new Location;
    	 
    	if($location->load($_POST)) {
    			if ($location->save()) {
    				Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Location {name} added successfully', ['name'=>$location->name])); //TRADUÇÃO
    					return $this->redirect(array('index'));
    			} else {
    					foreach($location->getErrors() as $attribute => $error) {
    						Yii::$app->getSession()->addFlash("error", $error[0]);
    					}
    					$location->clearErrors();
    			}
    	}

    	return $this->render('create',[
    			'location' => $location,
    			'domains' => self::whichDomainsCan('domainTopology/create'),
    	]);
    }
    
    public function actionUpdate($id){	
		$location = Location::findOne($id);
    	if(!isset($location)){
    		if(!self::can("domainTopology/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Location not found'));
    			return $this->redirect(array('index'));
    		}
    	}
    	if(!self::can("domainTopology/update", $location->getDomain()->one()->name)){
    		if(!self::can("domainTopology/read")) return $this->goHome();
    		else{
    			Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'You are not allowed to update on domain {domain}', ['domain' => $location->getDomain()->one()->name]));
    			return $this->redirect(array('index'));
    		}
    	}

    	if($location->load($_POST)) {
    			if ($location->save()) {
    					Yii::$app->getSession()->addFlash("success", Yii::t('topology', 'Location {name} updated successfully', ['name'=>$location->name]));
    					return $this->redirect(array('index'));
    			} else {
    					foreach($location->getErrors() as $attribute => $error) {
    						Yii::$app->getSession()->addFlash("error", $error[0]);
    					}
    					$location->clearErrors();
    			}
    	}
    	
    	return $this->render('update',[
    			'location' => $location,
    			'domains' => self::whichDomainsCan('domainTopology/update'),
    	]);
    }

    public function actionDelete(){
	    if(isset($_POST['delete'])){
    		foreach ($_POST['delete'] as $id) {
    			$location = Location::findOne($id);

                if(self::can('domainTopology/delete', $location->getDomain()->one()->name)){
	    			if ($location->delete())
                        Yii::$app->getSession()->addFlash('success', Yii::t('topology', 'Location {name} deleted', ['name'=>$location->name]));
	    			else
                        Yii::$app->getSession()->setFlash('error', Yii::t('topology', 'Error deleting location {name}', ['name'=>$location->name]));
    			}
    			else 
                    Yii::$app->getSession()->addFlash('warning', Yii::t('topology', 'Location {location} not deleted. You are not allowed to delete on domain {domain}', ['location' => $location->name, 'domain' => $location->getDomain()->one()->name]));
    		}
    	}
    	return $this->redirect(array('index'));
    }

    public function actionGetLocation($fields=null) {
        $query = Port::find()->innerJoin('meican_location', 'meican_location.id = meican_port.location_id')->select(['meican_location.name as location_name', 'meican_location.lat', 'meican_location.lng', 'network_id', 'location_id'])->asArray()->distinct(true);

        $fields ? $data = $query->select(explode(',',$fields))->all() : $data = $query->all();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data;
    }
    
}
