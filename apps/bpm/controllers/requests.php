<?php

include_once 'libs/meican_controller.php';
include_once 'libs/auth.php';

include_once 'apps/bpm/models/request_info.php';
include_once 'apps/circuits/models/reservation_info.php';
include_once 'apps/circuits/models/gri_info.php';
include_once 'apps/aaa/models/user_info.php';
include_once 'apps/topology/models/domain_info.php';

include_once 'libs/nuSOAP/lib/nusoap.php';

class requests extends MeicanController {

    public $modelClass = 'request_info';
    public function requests() {
        $this->app = 'bpm';
        $this->controller = 'requests';
        $this->defaultAction = 'show';
    }

    protected function renderEmpty(){
        $this->set(array(
            'title' => _("Requests"),
            'message' => _("You have no pending or finished request"),
            'link' => false
            ));
        parent::renderEmpty();
    }

    public function show() {
        if ($requests = $this->makeIndex(array('answerable' => 'yes'))) {
            $pending = array();
            $finished = array();

            foreach ($requests as $req) {
                if (!$req->response)
                    $pending[] = $req->getRequestInfo(TRUE);
                else
                    $finished[] = $req->getRequestInfo(TRUE);
            }
            $this->set(compact('pending', 'finished'));
        }
    }

//    public function createRequest () {
//
//        $domain = new domain_info();
//        $domains = $domain->fetch(FALSE);
//
//        $this->setArgsToBody($domains);
//        $this->addScript('bpmStrategy');
//        $this->render('createRequest');
//    }
//
//    public function getUsers() {
//
//        $dom_ip = $_POST['dom_ip'];
//
//        //consulta domínio remoto via web service para buscar os usuários do mesmo
//        $client = new nusoap_client("http://$dom_ip/".Configure::read('systemDirName')."bpm/ws", array('cache_wsdl' => 0));
//
//        $result = $client->call('getUsers');
//        $this->renderJson($result);
//    }

    public function reply($input) {
        $locId = NULL;
        if (array_key_exists('loc_id', $input)) {
            $locId = $input['loc_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $request = new request_info();
        $request->loc_id = $input['loc_id'];

        $result = $request->getRequestInfo(TRUE, TRUE, TRUE, TRUE);
        $result->available_bandwidth = NULL;
        
        $gri = new gri_info();
        
        $this->set(array(
            'res_id' => $result->resc_id,
            'res_name' => $result->resc_descr,
            'bandwidth' => $result->bandwidth,
            'usr_login' => $result->src_user,
            'gris' => $gri->getGrisToView($result->resc_id)
        ));
        $this->addScriptForLayout('requests');
        $this->set('request', $result);
        $this->render('reply2');
    }

    public function saveResponse($request) {
        $locId = NULL;
        if (array_key_exists('loc_id', $request)) {
            $locId = $request['loc_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        //insere no banco local
        $to_response = new request_info();
        $to_response->loc_id = $locId;
        $to_response->answerable = 'yes';
        $to_response->response = Common::POST('response');
        $to_response->message = Common::POST('message');

        if ($to_response->response()) {
            $this->setFlash(_('Response saved'), "success");
            $this->show();
            return;
        } else {
            $this->setFlash(_('Response NOT saved'), "error");
            $this->show();
            return;
        }
    }

    /**
     * @todo pensar em como apagar se request for de outro domínio
     */
    
    public function delete() {
        if ($requests = Common::POST("del_checkbox")) {
            debug('requests',$requests);
            
            $count = 0;
            
            foreach ($requests as $loc_id) {
                $request = new request_info();
                $request->loc_id = $loc_id;
                
                $req_result = $request->fetch(FALSE);
                
                $del_request = new request_info();
                $del_request->req_id = $req_result[0]->req_id;
                
                if ($requests_to_delete = $del_request->fetch(FALSE)) {
                    $were_deleted = TRUE;
                    foreach ($requests_to_delete as $r_del) {
                        $were_deleted &= $r_del->delete();
                    }
                    
                    if ($were_deleted)
                        $count++;
                }
            }
            
            switch ($count) {
                case 0:
                    $this->setFlash(_("No request was deleted"), 'warning');
                    break;
                case 1:
                    $this->setFlash(_("One request was deleted"), 'success');
                    break;
                default:
                    $this->setFlash("$count ". _("requests were deleted"), 'success');
                    break;
            }
        }

        $this->show();
    }

//    function notifyResponse($response) {
//        debug('acionando notify response', $response);
//
//        if ($response) {
//
//            $req = new request_info();
//            $req->req_id = $response['req_id'];
//            $req->setDom('dom_src', $response['dom_src_ip']);
//            $req->answerable = 'no';
//
//            if ($result = $req->updateTo(array('message' => $response['message'], 'response' => $response['response']), FALSE)) {
//                    if ($response['response'] == 'accept') {
//
//                        if ($req->updateTo(array('status' => 'SENT TO OSCARS'), FALSE)){
//                            //requisicao aceita deve enviar ao OSCARS
//                            debug('enviando ao OSCARS...');
//                            $reservation = $req->fetch(FALSE);
//                            $res = new oscars($reservation[0]->resource_id);
//                            if ($res->createReservation())
//                                return TRUE;
//                            else {
//                                debug('erro ao enviar ao oscars');
//                                return NULL;
//                            }
//                        }
//                    } else {
//                        //requisicao negada, termina
//                        debug('requisicao negada', $response['req_id']);
//                        return TRUE;
//                    }
//            } else {
//                //requisicao nao encontrada no banco local, requisicao nao enviada a este dominio
//                debug('req nao encontrada', $response['req_id']);
//                return NULL;
//            }
//        } else {
//            debug('notifyresponse without response set');
//            return NULL;
//        }
//    }
} //class requests

?>
