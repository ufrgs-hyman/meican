<?php

defined ('__FRAMEWORK') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'includes/common.inc';
include_once 'apps/bpm/models/request_info.inc';

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

    public function welcome(){
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

        //checar se tem acesso o a novas reservas
        $icons[0]->name = _('New Reservation');
        $icons[0]->figure = 'layouts/img/new_reservation.png';
        $icons[0]->link = array('app'=>'circuits', 'controller'=>'reservations','action'=>'page1');

        $icons[1]->name = _('Reservations');
        $icons[1]->figure = 'layouts/img/reservations_list.png';
        $icons[1]->link = array('app'=>'circuits', 'controller'=>'reservations','action'=>'show');

        $icons[2]->name = _('Requests');
        $icons[2]->figure = 'layouts/img/requests_1.png';
        $icons[2]->link = array('app'=>'bpm', 'controller'=>'requests','action'=>'show');

        $icons[3]->name = _('Management');
        $icons[3]->figure = 'layouts/img/management.png';
        $icons[3]->link = array('app'=>'aaa', 'controller'=>'users','action'=>'show');

        $this->setArgsToBody($icons);
        $this->render();


    }

}

?>
