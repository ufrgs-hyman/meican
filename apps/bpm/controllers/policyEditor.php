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
    
//    public function listWirings() {
//        $request = array(
//            'id' => null, 
//            'method' => null); //TODO: ler do post
//        //TODO: queries
//        $response = array (
//            'id' => $request['id'], 
//            'result' => NULL,
//            'error' => "unknown method '".$request['method']."' or incorrect parameters");
//        $this->renderJson($response);
//        
//        $hamehame = wirings::listWirings($language);
//    }
    
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
        
        $result = array('id' => $request['id'], 'result' => $workflows, 'error' => NULL);
        $this->renderJson($result);
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
