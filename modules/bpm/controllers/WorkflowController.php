<?php

namespace meican\bpm\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

use meican\base\components\DateUtils;
use meican\aaa\RbacController;
use meican\aaa\models\User;
use meican\aaa\models\UserDomainRole;
use meican\bpm\models\BpmWorkflow;
use meican\bpm\forms\WorkflowSearch;
use meican\topology\models\Device;
use meican\topology\models\Domain;
use meican\topology\models\Group;

class WorkflowController extends RbacController {
	
	public $enableCsrfValidation = false;
	
    public function actionIndex() {
    	$allowedDomains = self::whichDomainsCan('workflow/read');
    	
    	if(count($allowedDomains) < 1)return $this->goHome();
    	
    	$searchModel = new WorkflowSearch;
    	$data = $searchModel->searchByDomains(Yii::$app->request->get(), $allowedDomains);

    	return $this->render('index', array(
    			'searchModel' => $searchModel,
    			'data' => $data,
    	));
    }
    
    public function actionNew() {
		if(!self::can('workflow/create')){
			if(!self::can("workflow/read"))	return $this->goHome();
			else{
				Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to create workflows'));
				return $this->redirect(array('/bpm/workflow/index'));
			}	    
		}
    	return $this->render('indexCreate');
    }
    
    public function actionCreate($domainTop = null){
    	if($domainTop){
    		$domain = Domain::findOne(['name' => $domainTop]);
    		if($domain){
			    if(!self::can('workflow/create', $domain->name)){
			    	if(!self::can("workflow/read")) return $this->goHome();
            		else{
            			Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to create in domain {domain}', ['domain' => $domain->name]));
            			return $this->redirect(array('/bpm/workflow/index'));
            		}
            	}
		    	return $this->render('create', array(
		    		'domainTop' => $domainTop,
	    			'domainName' => $domain->name,
		    	));
    		};
    	};
    	if(!self::can("workflow/read")) return $this->goHome();
    	else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionUpdate($id = null){
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
	    		$domain = Domain::findOne(['name' => $workflow->domain]);
	    		if($domain){
			    	if(!self::can('workflow/update', $domain->name)){
			    		if(!self::can("workflow/read")) return $this->goHome();
            			else{
            				Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to edit in domain {domain}', ['domain' => $domain->name]));
            				return $this->redirect(array('/bpm/workflow/index'));
            			}
			    	}
		    		return $this->render('update', array(
		    				'id' => $id,
		    				'domainName' => $domain->name,
		    		));
		    	};
    		};
    	};
    	if(!self::can("workflow/read")) return $this->goHome();
    	else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionViewer($id = null){
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
	    		$domain = Domain::findOne(['name' => $workflow->domain]);
	    		if($domain){
		    		if(!self::can('workflow/read', $domain->name)){
		    			if(!self::can("workflow/read")) return $this->goHome();
            			else{
            				Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to read in domain {domain}', ['domain' => $domain->name]));
            				return $this->redirect(array('/bpm/workflow/index'));
            			}
		    		}
		    		$workflow = BpmWorkflow::findOne(['id' => $id]);
		    		return $this->render('indexViewer', array(
		    				'id' => $id,
		    				'domainName' => $domain->name,
		    				'workName' => $workflow->name,
		    		));
	    		};
    		};
    	};
    	if(!self::can("workflow/read")) return $this->goHome();
    	else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionEditorCreate($domainTop = null) {
    	if($domainTop){
    		$domain = Domain::findOne(['name' => $domainTop]);
    		if($domain){
			    if(!self::can('workflow/create', $domain->name)){
			    	if(!self::can("workflow/read")) return $this->goHome();
            		else{
            			Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to create in domain {domain}', ['domain' => $domain->name]));
            			return $this->redirect(array('/bpm/workflow/index'));
            		}
			    }
    	
		    	$ownerDomain = [];
		    	$ownerDomain[$domainTop] = $domain->name;
		    	
		    	$domains = Domain::find()->orderBy(['name' => SORT_ASC])->all();
		    	$allDomains = [];
		    	foreach($domains as $dom){
		    		$allDomains[$dom->name] = $dom->name;
		    	}

		    	$roles = $domain->getUserDomainsRoles()->all();
		    	
		    	$adminsNames = [];
		    	foreach($roles as $role):
		    		$adminsNames[$role->getUser()->id] = $role->getUser()->name;
		    	endforeach;
		    	
		    	foreach(User::find()->all() as $user):
		    		$usersNames[$user->id] = $user->name;
		    	endforeach;
		    	
		    	$groupsNames = [];
		    	foreach(Group::find()->where(['type' => Group::TYPE_DOMAIN])->all() as $group):
			    	$groupsNames[$group->id] = $group->name;
		    	endforeach;
		    	
		    	$devicesNames = [];
		    	foreach(Device::find()->where(['domain_id' => $domain->id])->all() as $device):
		    	$devicesNames[$device->id] = $device->name;
		    	endforeach;
		    	
		    	Yii::trace($roles);
		    	Yii::trace($usersNames);
		    	Yii::trace($groupsNames);
		    	Yii::trace($devicesNames);
		    	 
		    	return $this->renderPartial('editor', array(
		    			'owner_domain' => $ownerDomain,
		    			'domains' => $allDomains,
		    			'groups' => $groupsNames,
		    			'users' => $usersNames,
		    			'admins' => $adminsNames,
		    			'devices' => $devicesNames,
		    	));
    		};
    	};
    	if(!self::can("workflow/read")) return $this->goHome();
    	else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionEditorUpdate($id = null) {
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
				$domain = Domain::findOne(['name' => $workflow->domain]);
		    	if($domain){
			    	if(!self::can('workflow/update', $domain->name)){
			    		if(!self::can("workflow/read")) return $this->goHome();
            			else{
            				Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to edit in domain {domain}', ['domain' => $domain->name]));
            				return $this->redirect(array('/bpm/workflow/index'));
            			}
			    	}
			    	$ownerDomain = [];
			    	$ownerDomain[$domain->name] = $domain->name;
			    	
			    	$domains = Domain::find()->orderBy(['name' => SORT_ASC])->all();
			    	$allDomains = [];
			    	foreach($domains as $dom){
			    		$allDomains[$dom->name] = $dom->name;
			    	}
			    	
			    	$roles = $domain->getUserDomainsRoles()->all();
			    	 
			    	$adminsNames = [];
			    	foreach($roles as $role):
			    		$adminsNames[$role->getUser()->id] = $role->getUser()->name;
			    	endforeach;
			    	
			    	foreach(User::find()->all() as $user):
			    		$usersNames[$user->id] = $user->name;
			    	endforeach;
			    	
			    	$groupsNames = [];
			    	foreach(Group::find()->where(['type' => Group::TYPE_DOMAIN])->all() as $group):
				    	$groupsNames[$group->id] = $group->name;
			    	endforeach;
			    	
			    	$devicesNames = [];
			    	foreach(Device::find()->where(['domain_id' => $domain->id])->all() as $device):
			    	$devicesNames[$device->id] = $device->name;
			    	endforeach;
			    	
			    	Yii::trace($roles);
			    	Yii::trace($usersNames);
			    	Yii::trace($groupsNames);
			    	Yii::trace($devicesNames);
			    	 
			    	return $this->renderPartial('editor', array(
		    			'owner_domain' => $ownerDomain,
		    			'domains' => $allDomains,
		    			'groups' => $groupsNames,
		    			'users' => $usersNames,
		    			'admins' => $adminsNames,
			    		'devices' => $devicesNames,
			    		'id' => $_GET['id'],
			    	));
		    	};
    		};
	    };
	    if(!self::can("workflow/read")) return $this->goHome();
	    else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionEditorViewer($id = null) {
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
				$domain = Domain::findOne(['name' => $workflow->domain]);
		    	if($domain){
		    		if(!self::can('workflow/read', $domain->name)){
		    			if(!self::can("workflow/read")) return $this->goHome();
            			else{
            				Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to read in domain {domain}', ['domain' => $domain->name]));
            				return $this->redirect(array('/bpm/workflow/index'));
            			}
		    		}
		    		
			    	$ownerDomain = [];
			    	$ownerDomain[$domain->name] = $domain->name;
			    	 
			    	$domains = Domain::find()->all();
			    	$allDomains = [];
			    	foreach($domains as $dom){
			    		$allDomains[$dom->name] = $dom->name;
			    	}
			    
			    	$roles = $domain->getUserDomainsRoles()->all();

			    	$adminsNames = [];
			    	foreach($roles as $role):
			    		$adminsNames[$role->getUser()->id] = $role->getUser()->name;
			    	endforeach;
			    	
			    	foreach(User::find()->all() as $user):
			    		$usersNames[$user->id] = $user->name;
			    	endforeach;
			    	
			    	$groupsNames = [];
			    	foreach(Group::find()->where(['type' => Group::TYPE_DOMAIN])->all() as $group):
				    	$groupsNames[$group->id] = $group->name;
			    	endforeach;
			    	
			    	$devicesNames = [];
			    	foreach(Device::find()->where(['domain_id' => $domain->id])->all() as $device):
			    	$devicesNames[$device->id] = $device->name;
			    	endforeach;
			    	
			    	Yii::trace($roles);
			    	Yii::trace($usersNames);
			    	Yii::trace($groupsNames);
			    	Yii::trace($devicesNames);
			    	 
			    	return $this->renderPartial('viewer', array(
		    			'owner_domain' => $ownerDomain,
		    			'domains' => $allDomains,
		    			'groups' => $groupsNames,
		    			'users' => $usersNames,
		    			'admins' => $adminsNames,
			    		'devices' => $devicesNames,
			    		'id' => $_GET['id'],
			    	));
				};
    		};
	    };
	    if(!self::can("workflow/read")) return $this->goHome();
	    else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionLoadWorkflow() {
    	if(isset($_POST['id'])){
	    	$id = $_POST['id'];
	    	$workflow = BpmWorkflow::findOne(['id' => $id]);
	    	if($workflow){
		    	if($workflow->active==1) return 0;
		    	$json = json_decode($workflow->json, true);
		    	Yii::trace($json);
		    	$response = [];
		    	$response['id'] = $json['id'];
		    	$response['language'] = $json['params']['language'];
		    	$response['name'] = $json['params']['name'];
		    	$response['working'] = $json['params']['working'];
		    	return json_encode($response);
	    	} else return -1;
    	} else return -1;
    }
    
    public function actionViewWorkflow() {
    	if(isset($_POST['id'])){
    		$id = $_POST['id'];
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
	    		$json = json_decode($workflow->json, true);
	    		Yii::trace($json);
	    		$response = [];
	    		$response['id'] = $json['id'];
	    		$response['language'] = $json['params']['language'];
	    		$response['name'] = $json['params']['name'];
	    		$response['working'] = $json['params']['working'];
	    		return json_encode($response);
    		} else return -1;
    	} else return -1;
    }
    
    public function actionDelete($id = null) {
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
				$domain = Domain::findOne(['name' => $workflow->domain]);
		    	if($domain){
		    		if(self::can('workflow/delete', $domain->name)){
			    		if(BpmWorkflow::findOne(['id' => $id])->active == 0){
			    			BpmWorkflow::deleteAll(['in', 'id', $id]);
			    		}
			    		else {
			    			BpmWorkflow::disable($id);
			    			BpmWorkflow::deleteAll(['in', 'id', $id]);
			    		}
		    		}
		    		else Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to delete in domain {domain}', ['domain' => BpmWorkflow::findOne(['id' => $id])->getDomain()->one()->name]));
		    	}
    		}
    	}
    	
    	if(!self::can("workflow/read")) return $this->goHome();
        else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionCopy($id = null) {
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
				$domain = Domain::findOne(['name' => $workflow->domain]);
		    	if($domain){
				    if(!self::can('workflow/create', $domain->name)){
				    	Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to create in domain {domain}', ['domain' => $domain->name]));
				    	if(!self::can("workflow/read")) return $this->goHome();
            			else return $this->redirect(array('/bpm/workflow/index'));
				    }
		    		BpmWorkflow::findOne(['id' => $id])->copy();
		    	}
    		}
    	}
    	if(!self::can("workflow/read")) return $this->goHome();
        else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionActive($id = null) {
    	if($id){
    		$activeWorkflow = BpmWorkflow::findOne(['id' => $id]);
    		if($activeWorkflow){
				$domain = Domain::findOne(['name' => $activeWorkflow->domain]);
		    	if($domain){
		    		if(self::can('workflow/delete', $domain->name)){
			    		$oldWorkflow = BpmWorkflow::findOne(['domain' => $activeWorkflow->domain, 'active' => 1]);
			    	
			    		if($oldWorkflow){
			    			//Se são diferentes, pois pode estar tentando ativar o que ja está ativo
			    			if($oldWorkflow->id != $id){
					    		BpmWorkflow::disable($oldWorkflow->id);
					    		$activeWorkflow->active = 1;
					    		if (!$activeWorkflow->save()){
					    			Yii::$app->getSession()->setFlash('error', Yii::t("bpm", 'Unsuccessful enable the workflow {workflow} form domain {domain}', ['workflow' => $workflow->name, 'domain' => $workflow->getDomain()->one()->name]));
					    		}
			    			}
			    		}
			    		else {
			    			$activeWorkflow->active = 1;
			    			if (!$activeWorkflow->save()){
			    				Yii::$app->getSession()->setFlash('error', Yii::t("bpm", 'Unsuccessful enable the workflow {workflow} form domain {domain}', ['workflow' => $workflow->name, 'domain' => $workflow->getDomain()->one()->name]));
			    			}
			    		}
		    		}
		    		else Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to enable/disable in domain {domain}', ['domain' => BpmWorkflow::findOne(['id' => $id])->getDomain()->one()->name]));
		    	}
    		}
    	}
    	
    	if(!self::can("workflow/read")) return $this->goHome();
        else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionDisable($id = null) {
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
				$domain = Domain::findOne(['name' => $workflow->domain]);
		    	if($domain){
		    		if(self::can('workflow/delete', $domain->name)){
			    		if (!BpmWorkflow::disable($id)){
			    			Yii::$app->getSession()->setFlash('error', Yii::t("bpm", 'Unsuccessful disable the workflow {workflow} form domain {domain}', ['workflow' => $workflow->name, 'domain' => $workflow->getDomain()->one()->name]));
			    		}
					}
		    		else Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You are not allowed to enable/disable in domain {domain}', ['domain' => BpmWorkflow::findOne(['id' => $id])->getDomain()->one()->name]));
		    	}
    		}
    	}
    	if(!self::can("workflow/read")) return $this->goHome();
        else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    
    public function actionIsActive($id = null){
    	if($id){
	    	if(BpmWorkflow::findOne(['id' => $id])->active == 1){
	    		$domain = Domain::findOne(['name' => BpmWorkflow::findOne(['id' => $id])->domain]);
	    		if($domain) echo json_encode($domain->name);
	    		else echo 0;
	    	}
	    	else echo 0;
    	}
    }
    
    public function actionUpdateWorkflow($id = null) {
		$work = new BpmWorkflow();
		$work->saveWorkflow('update', $id);    	
    }
    
    public function actionSaveWorkflow() {
    	$work = new BpmWorkflow();
		$work->saveWorkflow();  
    }
    
    public function actionGetUserDomains(){
    	$domains = Domain::find()->orderBy(['name' => SORT_ASC])->all();
    	$domainsClean = [];
    	foreach($domains as $domain){
    			if(self::can('workflow/create', $domain->name))
    				$domainsClean[$domain->name] = $domain->name;
    	}
    	echo json_encode($domainsClean);
    }
    
}