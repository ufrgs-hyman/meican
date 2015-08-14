<?php

namespace app\modules\bpm\controllers;

use Yii;
use app\controllers\RbacController;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use app\models\Domain;
use app\models\Group;
use app\models\User;
use app\models\UserDomainRole;
use app\models\BpmWorkflow;
use app\components\DateUtils;
use app\modules\bpm\models\WorkflowSearch;

class WorkflowController extends RbacController {
	
	public $enableCsrfValidation = false;
	
    public function actionIndex() {
    	$searchModel = new WorkflowSearch;
    	$allowedDomains = self::whichDomainsCan('workflow/read');
    	$data = $searchModel->searchByDomains(Yii::$app->request->get(), $allowedDomains);

    	return $this->render('index', array(
    			'searchModel' => $searchModel,
    			'data' => $data,
    	));
    }
    
    public function actionNew() {
    	$permission = self::can('workflow/create');
		if(!$permission){
			Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to create workflows'));
	    	return $this->redirect(array('/bpm/workflow/index'));
	    }
    	return $this->render('indexCreate');
    }
    
    public function actionCreate($domainTop = null){
    	if($domainTop){
    		$domain = Domain::findOne(['name' => $domainTop]);
    		if($domain){
    			$permission = self::can('workflow/create', $domain->name);
			    if(!$permission){
			    	Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to create in domain {domain}', ['domain' => $domain->name]));
			    	return $this->redirect(array('/bpm/workflow/index'));
			    }
		    	return $this->render('create', array(
		    		'domainTop' => $domainTop,
	    			'domainName' => $domain->name,
		    	));
    		}
    		else return $this->redirect(array('/bpm/workflow/index'));
    	}
	    else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionUpdate($id = null){
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
	    		$domain = Domain::findOne(['name' => $workflow->domain]);
	    		if($domain){
	    			$permission = self::can('workflow/update', $domain->name);
			    	if(!$permission){
			    		Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to edit in domain {domain}', ['domain' => $domain->name]));
			    		return $this->redirect(array('/bpm/workflow/index'));
			    	}
		    		return $this->render('update', array(
		    				'id' => $id,
		    				'domainName' => $domain->name,
		    		));
		    	} else return $this->redirect(array('/bpm/workflow/index'));
    		} else return $this->redirect(array('/bpm/workflow/index'));
    	} else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionViewer($id = null){
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
	    		$domain = Domain::findOne(['name' => $workflow->domain]);
	    		if($domain){
	    			$permission = self::can('workflow/read', $domain->name);
		    		if(!$permission){
		    			Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to read in domain {domain}', ['domain' => $domain->name]));
		    			return $this->redirect(array('/bpm/workflow/index'));
		    		}
		    		$workflow = BpmWorkflow::findOne(['id' => $id]);
		    		return $this->render('indexViewer', array(
		    				'id' => $id,
		    				'domainName' => $domain->name,
		    				'workName' => $workflow->name,
		    		));
	    		} else return $this->redirect(array('/bpm/workflow/index'));
    		} else return $this->redirect(array('/bpm/workflow/index'));
    	} else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionEditorCreate($domainTop = null) {
    	if($domainTop){
    		$domain = Domain::findOne(['name' => $domainTop]);
    		if($domain){
    			$permission = self::can('workflow/create', $domain->name);
			    if(!$permission){
			    	Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to create in domain {domain}', ['domain' => $domain->name]));
			    	return $this->redirect(array('/bpm/workflow/index'));
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
		    	
		    	$usersNames = [];
		    	foreach(User::find()->all() as $user):
		    		$usersNames[$user->id] = $user->name;
		    	endforeach;
		    	
		    	$groupsNames = [];
		    	foreach(Group::find()->all() as $group):
			    	$groupsNames[$group->id] = $group->name;
		    	endforeach;
		    	
		    	Yii::trace($roles);
		    	Yii::trace($usersNames);
		    	Yii::trace($groupsNames);
		    	 
		    	return $this->renderPartial('editor', array(
		    			'owner_domain' => $ownerDomain,
		    			'domains' => $allDomains,
		    			'groups' => $groupsNames,
		    			'users' => $usersNames,
		    			'admins' => $adminsNames,
		    	));
    		} else return $this->redirect(array('/bpm/workflow/index'));
    	} else return $this->redirect(array('/bpm/workflow/index'));

    }
    
    public function actionEditorUpdate($id = null) {
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
				$domain = Domain::findOne(['name' => $workflow->domain]);
		    	if($domain){
			    	$permission = self::can('workflow/update', $domain->name);
			    	if(!$permission){
			    		Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to edit in domain {domain}', ['domain' => $domain->name]));
			    		return $this->redirect(array('/bpm/workflow/index'));
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
			    	
			    	$usersNames = [];
			    	foreach(User::find()->all() as $user):
			    		$usersNames[$user->id] = $user->name;
			    	endforeach;
			    	
			    	$groupsNames = [];
			    	foreach(Group::find()->all() as $group):
				    	$groupsNames[$group->id] = $group->name;
			    	endforeach;
			    	
			    	Yii::trace($roles);
			    	Yii::trace($usersNames);
			    	Yii::trace($groupsNames);
			    	 
			    	return $this->renderPartial('editor', array(
		    			'owner_domain' => $ownerDomain,
		    			'domains' => $allDomains,
		    			'groups' => $groupsNames,
		    			'users' => $usersNames,
		    			'admins' => $adminsNames,
			    		'id' => $_GET['id'],
			    	));
		    	} else return $this->redirect(array('/bpm/workflow/index'));
    		} else return $this->redirect(array('/bpm/workflow/index'));
	    } else return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionEditorViewer($id = null) {
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
				$domain = Domain::findOne(['name' => $workflow->domain]);
		    	if($domain){
		    		$permission = self::can('workflow/read', $domain->name);
		    		if(!$permission){
		    			Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to read in domain {domain}', ['domain' => $domain->name]));
		    			return $this->redirect(array('/bpm/workflow/index'));
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
			    	
			    	$usersNames = [];
			    	foreach(User::find()->all() as $user):
			    		$usersNames[$user->id] = $user->name;
			    	endforeach;
			    	
			    	$groupsNames = [];
			    	foreach(Group::find()->all() as $group):
				    	$groupsNames[$group->id] = $group->name;
			    	endforeach;
			    	
			    	Yii::trace($roles);
			    	Yii::trace($usersNames);
			    	Yii::trace($groupsNames);
			    	 
			    	return $this->renderPartial('viewer', array(
		    			'owner_domain' => $ownerDomain,
		    			'domains' => $allDomains,
		    			'groups' => $groupsNames,
		    			'users' => $usersNames,
		    			'admins' => $adminsNames,
			    		'id' => $_GET['id'],
			    	));
				} else return $this->redirect(array('/bpm/workflow/index'));
    		} else return $this->redirect(array('/bpm/workflow/index'));
	    } else return $this->redirect(array('/bpm/workflow/index'));
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
		    		$permission = self::can('workflow/delete', $domain->name);
		    		if($permission){
			    		if(BpmWorkflow::findOne(['id' => $id])->active == 0){
			    			BpmWorkflow::deleteAll(['in', 'id', $id]);
			    		}
			    		else {
			    			BpmWorkflow::disable($id);
			    			BpmWorkflow::deleteAll(['in', 'id', $id]);
			    		}
		    		}
		    		else Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to delete in domain {domain}', ['domain' => BpmWorkflow::findOne(['id' => $id])->getDomain()->one()->name]));
		    	}
    		}
    	}
    	
    	return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionCopy($id = null) {
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
				$domain = Domain::findOne(['name' => $workflow->domain]);
		    	if($domain){
			    	$permission = self::can('workflow/create', $domain->name);
				    if(!$permission){
				    	Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to create in domain {domain}', ['domain' => $domain->name]));
				    	return $this->redirect(array('/bpm/workflow/index'));
				    }
		    		BpmWorkflow::findOne(['id' => $id])->copy();
		    	}
    		}
    	}
    	return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionActive($id = null) {
    	if($id){
    		$activeWorkflow = BpmWorkflow::findOne(['id' => $id]);
    		if($activeWorkflow){
				$domain = Domain::findOne(['name' => $activeWorkflow->domain]);
		    	if($domain){
		    		$permission = self::can('workflow/delete', $domain->name);
		    		if($permission){
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
		    		else Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to enable/disable in domain {domain}', ['domain' => BpmWorkflow::findOne(['id' => $id])->getDomain()->one()->name]));
		    	}
    		}
    	}
    	
    	return $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionDisable($id = null) {
    	if($id){
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		if($workflow){
				$domain = Domain::findOne(['name' => $workflow->domain]);
		    	if($domain){
		    		$permission = self::can('workflow/delete', $domain->name);
		    		if($permission){
			    		if (!BpmWorkflow::disable($id)){
			    			Yii::$app->getSession()->setFlash('error', Yii::t("bpm", 'Unsuccessful disable the workflow {workflow} form domain {domain}', ['workflow' => $workflow->name, 'domain' => $workflow->getDomain()->one()->name]));
			    		}
					}
		    		else Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t allowed to enable/disable in domain {domain}', ['domain' => BpmWorkflow::findOne(['id' => $id])->getDomain()->one()->name]));
		    	}
    		}
    	}
    	return $this->redirect(array('/bpm/workflow/index'));
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