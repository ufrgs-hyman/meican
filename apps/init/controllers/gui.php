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
        $this->defaultAction = 'welcome';
        $this->setLayout('default');
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
        
        $icons = array();
        
        if ($acl->checkACL("create", 'urn_info')) {
            $icon = new stdClass();
            $icon->name = _('New reservation');
            $icon->figure = 'layouts/img/new_reservation.png';
            $icon->link = array('app' => 'circuits', 'controller' => 'reservations', 'action' => 'add');
            $icons[] = $icon;
        }

        if ($acl->checkACL("read", 'reservation_info')) {
            $icon = new stdClass();
            $icon->name = _('Reservations');
            $icon->figure = 'layouts/img/reservations_list.png';
            $icon->link = array('app' => 'circuits', 'controller' => 'reservations', 'action' => 'show');
            $icons[] = $icon;
        }

        if ($acl->checkACL("read", 'request_info')) {
            $icon = new stdClass();
            $icon->name = _('Requests');
            $icon->figure = 'layouts/img/requests_1.png';
            $icon->link = array('app' => 'bpm', 'controller' => 'requests', 'action' => 'show');
            $icons[] = $icon;
        }

        if ($acl->checkACL("read", 'group_info')) {
            $icon = new stdClass();
            $icon->name = _('Management');
            $icon->figure = 'layouts/img/management.png';
            $icon->link = array('app' => 'aaa', 'controller' => 'users', 'action' => 'show');
            $icons[] = $icon;
        }
        $this->setArgsToBody($icons);
        $this->render();


    }

}

?>
