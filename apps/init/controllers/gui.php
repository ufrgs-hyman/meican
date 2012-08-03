<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'libs/common.php';
include_once 'apps/bpm/models/request_info.php';
include_once 'libs/acl_loader.php';

class gui extends Controller {

    public function gui() {
        $this->app = 'init';
        $this->controller = 'gui';
        $this->defaultAction = 'welcome';
    }

    public function show() {
        Common::destroySessionVariable('scripts');
        Common::destroySessionVariable('script_vars');

        $args->last_view = Common::rescueVar('last_view');

        if ($args->last_view === FALSE) {
            //$app = Configure::read('mainApp');
            //$args->last_view = "app=$app";
            $args->last_view =  "app=init&controller=gui&action=welcome";
        }

        if (!(Common::getSessionVariable('welcome_loaded')))
            $args->last_view =  "app=init&controller=gui&action=welcome";

        //$args->last_view = "app=init&controller=gui&action=welcome";
        $this->setArgsToBody($args);

        $this->render();
    }

    public function welcome() {
        $request = new request_info();

        if ($noReq = $request->checkRequests()) {
            if ($noReq == 1)
                $noReq = "one";
            $noReq = "<a href=\"".  Dispatcher::getInstance()->url(array('app' => 'bpm', 'controller' => 'requests')) . "\">$noReq</a>";
            $msg = _('You have') . " $noReq " . _('new requests to be authorized');                
            $this->setFlash($msg, 'warning');
        }
        $this->render('welcome');
    }
    
    public function language($pass){
        Language::getInstance()->setLanguage($pass[0].'.utf8');
        header('HTTP/1.1 405 Change Language');
    }
    
    function clearCache(){
        apc_clear_cache('user');
        $this->autoRender = false;
        $this->output = 'Cache cleared';
    }

}

?>
