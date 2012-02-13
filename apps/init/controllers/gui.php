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
        $this->setAction('welcome');

        $request = new request_info();

        if ($noReq = $request->checkRequests()) {
            
            switch ($noReq) {
                case 1:
                    $msg = _('You have one new request to be authorized');
                    break;
                default:
                    $msg = _('You have');
                    $msg .= " $noReq ";
                    $msg .= _('new requests to be authorized');
                    break;
            }
                
            $this->setFlash($msg, 'warning');
        }
        $this->render();
    }

}

?>
