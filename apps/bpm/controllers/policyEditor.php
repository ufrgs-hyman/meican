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
            'title' => _("Workflows"),
            'message' => sprintf(_("No %s created"), _("workflow")).". "._("Please, click the button below to add a new one")
        ));
        parent::renderEmpty();
    }

    public function show() {
        if ($allWorkflows = $this->makeIndex(array('useACL' => false))) {
            $workflows = array();

            foreach ($allWorkflows as $w) {
                $workflow = new stdClass();
                $workflow->id = $w->id;
                $workflow->name = $w->name;
                $workflow->language = $w->language;
                $workflow->dom_id = $w->dom_id;
                
                $dom_tmp = new domain_info();
                $dom_tmp->dom_id = $w->dom_id;
                $domain = $dom_tmp->get('dom_descr');
                $workflow->domain = $domain ? $domain : _("Unknown");
                
                $workflow->status = $w->status;
                $workflow->status_descr = $w->status ? _("Enabled") : _("Disabled");

                $workflows[] = $workflow;
            }
            $this->setArgsToBody($workflows);
            $this->render('show');
        }
    }
    
    public function add_form() {
        $user_info = new user_info();
        $allUsers = $user_info->fetch();
        $users = Common::arrayExtractAttr($allUsers, 'usr_login');
        
        $this->setArgsToScript(array(
            "workflow_to_load" => "Teste",
            "load_workflow" => 0,
            "users" => $users
        ));
        $this->render('add');
    }

    public function show_frame() {
        $this->layout = 'empty';
        $this->render('show_frame');
    }
    
    public function edit($workflow_id_array) {
        $id = NULL;
        if (array_key_exists('id', $workflow_id_array)) {
            $id = $workflow_id_array['id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $workflows_info = new workflows_info();
        $workflows_info->id = $id;
        $workflow = $workflows_info->fetch(false);

        if (!$workflow) {
            $this->setFlash(_("Workflow not found"), "fatal");
            $this->show();
            return;
        }
        
        $workflows = array();
        $wkf = new stdClass();
        $wkf->id = $workflow[0]->id;
        $wkf->name = $workflow[0]->name;
        $wkf->working = $workflow[0]->working;
        $wkf->language = $workflow[0]->language;

        $workflows[] = $wkf;
        
        $this->setArgsToScript(array(
            "workflow_to_load" => "teste",
            "load_workflow" => 1,
            "workflows" => $workflows,
            "workflow" => $wkf,
            "workflow_name" => $wkf->name
        ));
        
        $this->render('add');
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
