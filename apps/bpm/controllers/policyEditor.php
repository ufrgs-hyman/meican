<?php

include_once 'libs/meican_controller.php';

include_once 'apps/aaa/models/user_info.php';
include_once 'apps/aaa/models/group_info.php';
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
    
    private function buildArgs()  {
        //TODO: lembre-se isso está sendo passado para o load_frame e para o frame
        $user_info = new user_info();
        $allUsers = $user_info->fetch();
        $users = Common::arrayExtractAttr($allUsers, 'usr_login');
        
        $group_info = new group_info();
        $allGroups = $group_info->fetch();
        $groups = Common::arrayExtractAttr($allGroups, 'grp_descr');
        
        $domain_info = new domain_info();
        $allDomains = $domain_info->fetch(false);
        
        $owner_domains = array();
        foreach ($allDomains as $domain)
            $owner_domains[$domain->dom_id] = $domain->dom_descr;
        
        $domains = Common::arrayExtractAttr($allDomains, 'topology_id');
        
        $lang_temp = explode(".", Language::getInstance()->getLanguage());
        $language = $lang_temp[0];
        
        
        return compact('users', 'groups', 'domains', 'language', 'owner_domains')
            +array(
            "string_workflow_name" => _("Workflow name"),
            "string_enter_title" => _("Enter a title"),
            "string_choose_name" => _("Please choose a name"),
            "string_save" => _("Workflow saved"),
        );
    }

    public function show() {
        if ($allWorkflows = $this->makeIndex(array('useACL' => false))) {
            $workflows = array();

            foreach ($allWorkflows as $w) {
                $workflow = new stdClass();
                $workflow->id = $w->id;
                $workflow->name = $w->name;
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
        $dom = new domain_info();
        $allDomains = $dom->fetch();
        $domains_to_body = array();
        foreach ($allDomains as $d) {
            $domain = new stdClass();
            $domain->dom_id = $d->dom_id;
            $domain->dom_descr = $d->dom_descr;
            $domains_to_body[] = $domain;
        }

        $this->setArgsToBody($domains_to_body);

        $args = array(
            "load_workflow" => 0,
        );

        $this->setArgsToScript(array_merge($args, $this->buildArgs()));
        $this->render('load_frame');
    }

    public function show_frame() {
        $this->setArgsToScript($this->buildArgs());
        
        $this->layout = 'empty';
        $this->render('frame');
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
        
        $dom = new domain_info();
        $allDomains = $dom->fetch();
        $domains_to_body = array();
        foreach ($allDomains as $d) {
            $domain = new stdClass();
            $domain->dom_id = $d->dom_id;
            $domain->dom_descr = $d->dom_descr;
            $domains_to_body[] = $domain;
        }
        $args = new stdClass();
        $args->domains = $domains_to_body;
        $args->dom_id = $workflow[0]->dom_id;

        $this->setArgsToBody($args);
        
        $wkf = new stdClass();
        $wkf->id = $workflow[0]->id;
        $wkf->name = $workflow[0]->name;
        $wkf->working = $workflow[0]->working;
        $wkf->language = $workflow[0]->language;

        $args = array(
            "load_workflow" => 1,
            "workflow" => $wkf,
        );
        
        $this->setArgsToScript(array_merge($args, $this->buildArgs()));
        $this->render('load_frame');
    }
    
    
    
    public function duplicate($workflow_id_array) {
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
        $workflow = $workflow[0];
        
        $workflow->name .= ' (copy)';
        
        $result = new stdClass();
        $workflow->id = null;
        if ($added = $workflow->insert()) {
            $result->success = true;
            $result->id = $added->id;
        } else {
            $result->success = false;
            $result->id = NULL;
        }
        
    }
    
    
    //TODO:EDITOR
    public function deploy($workflow_id_array) {
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
        $workflow = $workflow[0];
        $wirit = json_decode($workflow->working);
        debug($wirit->modules);
        foreach ($wirit->modules as $module){
            debug($module->name);
            debug($module->value);
        }
        
        
    }
    
//    public function listWorkflows() {
//        $request = json_decode(file_get_contents('php://input'),true);
//        
//        $workflow_info = new workflows_info();
//        $allWorkflows = $workflow_info->fetch(false);
//        
//        $workflows = array();
//        if ($allWorkflows) {
//            foreach ($allWorkflows as $w) {
//                $workflow = new stdClass();
//                $workflow->id = $w->id;
//                $workflow->name = $w->name;
//                $workflow->working = $w->working;
//                $workflow->language = $w->language;
//                
//                $workflows[] = $workflow;
//            }
//        }
//        
//        $response = array('id' => $request['id'], 'result' => $workflows, 'error' => NULL);
//        $this->renderJson($response);
//    }
    
    public function saveWorkflow() {
        $request = json_decode(file_get_contents('php://input'),true);
        CakeLog::debug(print_r($request,true));
        
        $params = $request['params'];
       // $working = json_decode($params['working']);
        
        $work_info = new workflows_info();
        $work_info->name = $params['name'];
        $work_info->language = $params['language'];
        $work_info->working = $params['working'];
        //$work_info->dom_id = $working->properties->domains_owner;
        $work_info->status = $params['status'];
        
        $result = new stdClass();
        if ($params['id']) {
            $work_info->id = $params['id'];
            $result->success = $work_info->update();
            $result->id = $work_info->id;
        } else {
            if ($added = $work_info->insert()) {
                $result->success = true;
                $result->id = $added->id;
            } else {
                $result->success = false;
                $result->id = NULL;
            }
        }

        $response = array ('id' => $request['id'],'result' => $result,'error' => NULL);
        $this->renderJson($response);
    }
    
    public function delete() {
        if ($del_workflows = Common::POST('del_checkbox')) {
            foreach ($del_workflows as $id) {
                $workflow = new workflows_info();
                $workflow->id = $id;
                $tmp = $workflow->fetch(false);
                $result = $tmp[0];
                if ($workflow->delete(false))
                    $this->setFlash(_("Workflow") . " '$result->name' " . _("deleted"), 'success');
            }
        }

        $this->show();
    }

}
