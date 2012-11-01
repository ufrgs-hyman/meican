<?php

include_once 'libs/meican_controller.php';

include_once 'apps/bpm/models/request_info.php';
include_once 'apps/aaa/models/user_info.php';
include_once 'apps/topology/models/domain_info.php';

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
        
        $this->render('show');
    }
    
    

}

//class requests
?>
