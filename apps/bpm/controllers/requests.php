<?php

include_once 'libs/controller.php';
include_once 'apps/bpm/models/request_info.php';
include_once 'libs/auth.php';
include_once 'apps/aaa/models/user_info.php';
include_once 'apps/topology/models/domain_info.php';
include_once 'apps/circuits/models/oscars.php';

include_once 'libs/nuSOAP/lib/nusoap.php';

class requests extends Controller {

    public function requests() {
        $this->app = 'bpm';
        $this->controller = 'requests';
        $this->defaultAction = 'show';
    }

    public function show() {

        $requests = new request_info();

        $temp = $requests->getRequestInfo(TRUE);
        //$endpoint =  "http://143.54.12.185/dirname/main.php?bpm/ws";
        //$ws_client = new nusoap_client($endpoint, array('cache_wsdl' => 0));
        //$usr = array('usr_name' => 'Pietro Biasuz');
        //$grp = array('grp_id' => 2);
        //$result = $ws_client->call('getUsers', array('usr'=>$usr));
        //$result = $ws_client->call('getGroups');
        //$res_id_list = array(2,1,2);
        //$result = $ws_client->call('getResInfo',array(2));
        //$result = $ws_client->call('getTimerInfo',array(2));

        //$result = $ws_client->call('getFlowInfo', array(1));
        //$urn_array = array('urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC','error','blablalblablabla');
        //$result = $ws_client->call('getURNInfo',array('urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC'));
        //$result = $ws_client->call('getURNDetails',array('urn_string' => 'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFSC'));
        //$result = $ws_client->call('getURNDetails');

        if ($temp) {
            foreach ($temp as $t) {
               if ($t->answerable == 'yes')
                if (!$t->response)
                    $pending[] = $t;
                else $finished[] = $t;
            }

            //debug('temp',$temp);
            $args->pending = $pending;
            $args->finished = $finished;

            $this->setArgsToBody($args);
        }

        $this->render('show');
    } //function show

    public function createRequest () {

        $domain = new domain_info();
        $domains = $domain->fetch(FALSE);

        $this->setArgsToBody($domains);
        $this->addScript('bpmStrategy');
        $this->render('createRequest');
    }

    public function getUsers() {

        $dom_ip = $_POST['dom_ip'];

        //consulta domínio remoto via web service para buscar os usuários do mesmo
        $client = new nusoap_client("http://$dom_ip/".Configure::read('systemDirName')."bpm/ws", array('cache_wsdl' => 0));

        $result = $client->call('getUsers');
        $this->renderJson($result);
    }

    public function replyRequest($input) {

        $request = new request_info();

        $request->loc_id = $input['loc_id'];

        $result = $request->getRequestInfo(TRUE, TRUE, TRUE, TRUE);

        //debug('result',$result);

        $dom = new domain_info();
        $dom->dom_ip = $result[0]->flow_info['src_dom_ip'];
        $s_result = $dom->fetch(FALSE);

        if ($s_result)
            $result[0]->flow_info['src_dom'] = $s_result[0]->dom_descr;
        else {
            $result[0]->flow_info['src_dom'] = _('Unknown');
        }

        $dom->dom_ip = $result[0]->flow_info['dst_dom_ip'];
        $d_result = $dom->fetch(FALSE);

        if ($d_result)
            $result[0]->flow_info['dst_dom'] = $d_result[0]->dom_descr;
        else {
            $result[0]->flow_info['dst_dom'] = _('Unknown');
        }

        $args->request = $result[0];
        $this->setArgsToBody($args);
        $this->render('replyRequest');

    }//function replyRequest

    function saveResponse($request) {

        if ($request) {
            //insere no banco local
            $to_response = new request_info();
            $to_response->loc_id = $request['loc_id'];
            $to_response->answerable = 'yes';
            $to_response->response = Common::POST('response');
            $to_response->message = Common::POST('message');

            if ($to_response->response()) {
                $this->setFlash(_('Response saved'), "success");
                $this->show();
                return;
            }
            else {
                $this->setFlash(_('Response NOT saved'), "error");
                $this->show();
                return;
            }
        }
    }

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
                $del_request->dom_src = $req_result[0]->dom_src;
                
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
