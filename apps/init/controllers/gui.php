<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'includes/common.inc';
include_once 'apps/bpm/models/request_info.inc';
include_once 'libs/acl_loader.inc';

class gui extends Controller {

    public function gui() {
        $this->app = 'init';
        $this->controller = 'gui';
        $this->defaultAction = 'show';
        $this->setLayout('empty');
    }

    public function show() {
        Common::destroySessionVariable('scripts');
        Common::destroySessionVariable('script_vars');

        $args->last_view = Common::rescueVar('last_view');

        if ($args->last_view === FALSE) {
            //$app = Framework::getMainApp();
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
        $this->setLayout('default');

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

        //checar se tem acesso a novas reservas
        $acl = new AclLoader();
        
        $ind=0;
        
        if ($acl->checkACL("create", 'urn_info')) {
            $icons[$ind]->name = _('New reservation');
            $icons[$ind]->figure = 'layouts/img/new_reservation.png';
            $icons[$ind]->link = array('app' => 'circuits', 'controller' => 'reservations', 'action' => 'reservation_add');
            $ind++;
        }

        if ($acl->checkACL("read", 'reservation_info')) {
            $icons[$ind]->name = _('Reservations');
            $icons[$ind]->figure = 'layouts/img/reservations_list.png';
            $icons[$ind]->link = array('app' => 'circuits', 'controller' => 'reservations', 'action' => 'show');
            $ind++;
        }

        $icons[$ind]->name = _('Requests');
        $icons[$ind]->figure = 'layouts/img/requests_1.png';
        $icons[$ind]->link = array('app' => 'bpm', 'controller' => 'requests', 'action' => 'show');
        $ind++;

        if ($acl->checkACL("read", 'user_info')) {
            $icons[$ind]->name = _('Management');
            $icons[$ind]->figure = 'layouts/img/management.png';
            $icons[$ind]->link = array('app' => 'aaa', 'controller' => 'users', 'action' => 'show');
            $ind++;
        }

        $this->setArgsToBody($icons);
        $this->render();


    }

}

?>
