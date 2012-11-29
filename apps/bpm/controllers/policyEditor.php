<?php

include_once 'libs/meican_controller.php';

include_once 'apps/bpm/models/request_info.php';
include_once 'apps/aaa/models/user_info.php';
include_once 'apps/topology/models/domain_info.php';

include_once 'apps/bpm/models/workflows_info.php';

class policyEditor extends MeicanController {

    public $modelClass = 'workflows_info';

    protected function renderEmpty() {
        $this->set(array(
            'title' => _("Policy Editor"),
            'link' => false
        ));
        parent::renderEmpty();
    }

    public function show() {
        //$this->addScriptForLayout('policyEditor.js');
        $this->render('show');
    }

    public function show_frame() {
        //$this->addScriptForLayout('policyEditor.js');
        $this->layout = 'empty';
        $this->render('show_frame');
    }
    
    public function listWorkflows() {
        $request = json_decode(file_get_contents('php://input'),true);
        
        $workflow_info = new workflows_info();
        $allWorkflows = $workflow_info->fetch(false);
        
        $workflows = array();
        if ($allWorkflows) {
            foreach ($allWorkflows as $w) {
                $workflow = new stdClass();
                $workflow->id = $w->id;
                $workflow->name = $w->name;
                $workflow->working = $w->working;
                $workflow->language = $w->language;
                
                $workflows[] = $workflow;
            }
        }
        
        $response = array('id' => $request['id'], 'result' => $workflows, 'error' => NULL);
        $this->renderJson($response);
    }
    
    public function saveWorkflow() {
        $request = json_decode(file_get_contents('php://input'),true);
        CakeLog::debug(print_r($request,true));
        
        $params = $request['params'];
        
        $work_info = new workflows_info();
        $work_info->name = $params['name'];
        $work_info->language = $params['language'];
        $work_info->working = $params['working'];
        $work_info->dom_id = 1;
        $work_info->status = 0;
        
        if ($work_info->insert())
            $result = true;
        else
            $result = NULL;
        
        $response = array ('id' => $request['id'],'result' => $result,'error' => NULL);
        $this->renderJson($response);
    }

    public function loadWirings() {
        $request = array(
            'id' => null, 
            'method' => null); //TODO: ler do post
        //TODO: queries
        $response = array (
            'id' => $request['id'], 
            'result' => NULL,
            'error' => "unknown method '".$request['method']."' or incorrect parameters");
        $this->renderJson($response);
    }
    
    public function deleteWiring() {
        $request = array(
            'id' => null, 
            'method' => null); //TODO: ler do post
        //TODO: queries
        $response = array (
            'id' => $request['id'], 
            'result' => NULL,
            'error' => "unknown method '".$request['method']."' or incorrect parameters");
        $this->renderJson($response);
    }
    
    public function saveWiring() {
        $request = array(
            'id' => null, 
            'method' => null); //TODO: ler do post
        //TODO: queries
        $response = array (
            'id' => $request['id'], 
            'result' => NULL,
            'error' => "unknown method '".$request['method']."' or incorrect parameters");
        $this->renderJson($response);
    }
    

}
