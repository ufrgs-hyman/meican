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

class WorkflowController extends RbacController {
	
	public $enableCsrfValidation = false;
	
    public function actionIndex() {
    	$workflows = BpmWorkflow::find()->orderBy(['domain_id' => SORT_ASC]);

    	//Vetor para armazenar os workflows que o usuário tem permissão para ver
    	$workflowsClean = [];
    	//Percorre todos os workflows
    	foreach($workflows->all() as $work){
    			if(self::can('workflow/read', $work->domain_id))$workflowsClean[$work->id] = $work;
    	}
    	
    	$dataProvider = new ArrayDataProvider([
    			'allModels' => $workflowsClean,
    			'sort' => false,
    			'pagination' => false,
    	]);
    	
    	return $this->render('index', array(
    			'workflows' => $dataProvider
    	));
    }
    
    public function actionNew() {
    	self::canRedir('workflow/create');
    	
    	return $this->render('indexCreate');
    }
    
    public function actionCreate($domainId = null){
    	if($domainId){
    		self::canRedir('workflow/create', $domainId);
    		$domain = Domain::findOne(['id' => $domainId]);
    		if($domain)
	    		return $this->render('create', array(
		    			'domainId' => $domainId,
	    				'domainName' => $domain->name,
		    	));
    	}
	    else $this->actionIndex();
    }
    
    public function actionUpdate($id = null){

    	if($id){
    		self::canRedir('workflow/update', BpmWorkflow::findOne(['id' => $id])->domain_id);
    		
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		$domain = Domain::findOne(['id' => $workflow->domain_id]);
    		return $this->render('update', array(
    				'id' => $id,
    				'domainName' => $domain->name,
    		));
    	}
    }
    
    public function actionViewer($id = null){
    	if($id){
    		self::canRedir('workflow/read', BpmWorkflow::findOne(['id' => $id])->domain_id);
    		
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		$domain = Domain::findOne(['id' => $workflow->domain_id]);
    		return $this->render('indexViewer', array(
    				'id' => $id,
    				'domainName' => $domain->name,
    				'workName' => $workflow->name,
    		));
    	}
    }
    
    public function actionEditorCreate($domainId = null) {
    	if($domainId){
	    	self::canRedir('workflow/create', $domainId);
    	
	    	$domain = Domain::findOne(['id' => $domainId]);
	    	$ownerDomain = [];
	    	$ownerDomain[$domain->id] = $domain->name;
	    	
	    	$domains = Domain::find()->all();
	    	$allDomains = [];
	    	foreach($domains as $dom){
	    		$allDomains[$dom->id] = $dom->topology;
	    	}
	    	 
	    	$roles = $domain->getUserDomainsRoles()->all();
	    	
	    	$usersNames = [];
	    	foreach($roles as $role):
	    		$usersNames[$role->getUser()->id] = $role->getUser()->name;
	    	endforeach;
	    	
	    	$groupsNames = [];
	    	foreach($roles as $role):
		    	$groupsNames[$role->getGroup()->id] = $role->getGroup()->name;
	    	endforeach;
	    	
	    	Yii::trace($roles);
	    	Yii::trace($usersNames);
	    	Yii::trace($groupsNames);
	    	 
	    	return $this->renderPartial('editor', array(
	    			'owner_domain' => $ownerDomain,
	    			'domains' => $allDomains,
	    			'groups' => $groupsNames,
	    			'users' => $usersNames,
	    	));
    	}
    	else $this->redirect(array('/bpm/workflow/index'));

    }
    
    public function actionEditorUpdate($id = null) {
    	if($id){
	    	self::canRedir('workflow/update', BpmWorkflow::findOne(['id' => $id])->domain_id);
    	
	    	$domain = BpmWorkflow::findOne(['id'=>$id])->getDomain()->one();
	    	$ownerDomain = [];
	    	$ownerDomain[$domain->id] = $domain->name;
	    	
	    	$domains = Domain::find()->all();
	    	$allDomains = [];
	    	foreach($domains as $dom){
	    		$allDomains[$dom->id] = $dom->topology;
	    	}
	    	 
	    	$roles = $domain->getUserDomainsRoles()->all();
	    	
	    	$usersNames = [];
	    	foreach($roles as $role):
	    		$usersNames[$role->getUser()->id] = $role->getUser()->name;
	    	endforeach;
	    	
	    	$groupsNames = [];
	    	foreach($roles as $role):
		    	$groupsNames[$role->getGroup()->id] = $role->getGroup()->name;
	    	endforeach;
	    	
	    	Yii::trace($roles);
	    	Yii::trace($usersNames);
	    	Yii::trace($groupsNames);
	    	 
	    	return $this->renderPartial('editor', array(
	    			'owner_domain' => $ownerDomain,
	    			'domains' => $allDomains,
	    			'groups' => $groupsNames,
	    			'users' => $usersNames,
	    			'id' => $_GET['id'],
	    	));
	    }
	    else $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionEditorViewer($id = null) {
    	
    	if($id){
	    	self::canRedir('workflow/read', BpmWorkflow::findOne(['id' => $id])->domain_id);
	    	
	    	$domain = BpmWorkflow::findOne(['id'=>$id])->getDomain()->one();
	    	$ownerDomain = [];
	    	$ownerDomain[$domain->id] = $domain->name;
	    	 
	    	$domains = Domain::find()->all();
	    	$allDomains = [];
	    	foreach($domains as $dom){
	    		$allDomains[$dom->id] = $dom->name;
	    	}
	    
	    	$roles = $domain->getUserDomainsRoles()->all();
	    	 
	    	$usersNames = [];
	    	foreach($roles as $role):
	    	$usersNames[$role->getUser()->id] = $role->getUser()->name;
	    	endforeach;
	    	 
	    	$groupsNames = [];
	    	foreach($roles as $role):
	    	$groupsNames[$role->getGroup()->id] = $role->getGroup()->name;
	    	endforeach;
	    	 
	    	Yii::trace($roles);
	    	Yii::trace($usersNames);
	    	Yii::trace($groupsNames);
	    
	    	return $this->renderPartial('viewer', array(
	    			'owner_domain' => $ownerDomain,
	    			'domains' => $allDomains,
	    			'groups' => $groupsNames,
	    			'users' => $usersNames,
	    			'id' => $_GET['id'],
	    	));
    	}
    	else $this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionLoadWorkflow() {
    	if(isset($_POST['id'])){
	    	$id = $_POST['id'];
	    	$workflow = BpmWorkflow::findOne(['id' => $id]);
	    	if($workflow->active==1) return 0;
	    	$json = json_decode($workflow->json, true);
	    	Yii::trace($json);
	    	$response = [];
	    	$response['id'] = $json['id'];
	    	$response['language'] = $json['params']['language'];
	    	$response['name'] = $json['params']['name'];
	    	$response['working'] = $json['params']['working'];
	    	return json_encode($response);
    	}
    	else return -1;
    }
    
    public function actionViewWorkflow() {
    	if(isset($_POST['id'])){
    		$id = $_POST['id'];
    		$workflow = BpmWorkflow::findOne(['id' => $id]);
    		$json = json_decode($workflow->json, true);
    		Yii::trace($json);
    		$response = [];
    		$response['id'] = $json['id'];
    		$response['language'] = $json['params']['language'];
    		$response['name'] = $json['params']['name'];
    		$response['working'] = $json['params']['working'];
    		return json_encode($response);
    	}
    	else return -1;
    }
    
    public function actionDelete() {
    	if(isset($_GET['id'])){
    		$id = $_GET['id'];
    		$permission = self::can('workflow/delete', BpmWorkflow::findOne(['id' => $id])->domain_id);
    		
    		if($permission){
	    		if(BpmWorkflow::findOne(['id' => $id])->active == 0){
		    		BpmWorkflow::deleteAll(['in', 'id', $id]);
	    		}
	    		else {
	    			BpmWorkflow::disable($id);
	    			BpmWorkflow::deleteAll(['in', 'id', $id]);
	    		}
    		}
    		else Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t have permission for domain {domain}', ['domain' => BpmWorkflow::findOne(['id' => $id])->getDomain()->one()->name]));
    	}
    	
    	$this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionCopy($id = null) {
    	if($id){
    		self::can('workflow/create', BpmWorkflow::findOne(['id' => $id])->domain_id, true);
    		BpmWorkflow::findOne(['id' => $id])->copy();
    	}
    	$this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionActive($id = null) {
    	
    	if($id){
    		$activeWorkflow = BpmWorkflow::findOne(['id' => $id]);
    		
    		self::canRedir('workflow/update', BpmWorkflow::findOne(['id' => $id])->domain_id);
    		
    		$oldWorkflow = BpmWorkflow::findOne(['domain_id' => $activeWorkflow->domain_id, 'active' => 1]);
    	
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
    	
    	$this->redirect(array('/bpm/workflow/index'));
    }
    
    public function actionDisable($id = null) {
    	if($id){
    		$permission = self::can('workflow/update', BpmWorkflow::findOne(['id' => $id])->domain_id);
    		
    		if($permission){
	    		if (!BpmWorkflow::disable($id)){
	    			Yii::$app->getSession()->setFlash('error', Yii::t("bpm", 'Unsuccessful disable the workflow {workflow} form domain {domain}', ['workflow' => $workflow->name, 'domain' => $workflow->getDomain()->one()->name]));
	    		}
			}
    		else Yii::$app->getSession()->setFlash('warning', Yii::t("bpm", 'You don`t have permission for domain {domain}', ['domain' => BpmWorkflow::findOne(['id' => $id])->getDomain()->one()->name]));
    	}

    	
    	$this->redirect(array('/bpm/workflow/index'));
    }
    
    
    public function actionIsActive($id = null){
    	if($id){
	    	if(BpmWorkflow::findOne(['id' => $id])->active == 1) echo json_encode(Domain::findOne(['id' => BpmWorkflow::findOne(['id' => $id])->domain_id])->name);
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
    	$domains = Domain::find()->all();
    	$domainsClean = [];
    	foreach($domains as $domain){
    			if(self::can('workflow/create', $domain->id))
    				$domainsClean[$domain->id] = $domain->name;
    	}
    	echo json_encode($domainsClean);
    }
    
}