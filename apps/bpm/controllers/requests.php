<?php

include_once 'libs/controller.php';
include_once 'apps/bpm/models/request_info.inc';
include_once 'includes/auth.inc';
include_once 'apps/aaa/models/user_info.inc';
include_once 'apps/domain/models/domain_info.inc';
include_once 'apps/circuits/models/oscars.php';

include_once 'includes/nuSOAP/lib/nusoap.php';

class requests extends Controller {

    public function requests() {
        $this->app = 'bpm';
        $this->controller = 'requests';
        $this->defaultAction = 'show';
    }

    public function show() {
        $this->setAction('show');

        $requests = new request_info();

        $temp = $requests->getRequestInfo(TRUE);
        //$endpoint =  "http://143.54.12.185/Framework::$systemDirName/main.php?app=bpm&services&wsdl";
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

            //Framework::debug('temp',$temp);
            $args->pending = $pending;
            $args->finished = $finished;

            $this->setArgsToBody($args);
        }

        $this->render();
    } //function show

    public function createRequest () {

        $this->setAction('createRequest');

        $domain = new domain_info();
        $domains = $domain->fetch(FALSE);

        $this->setArgsToBody($domains);
        $this->addScript('bpmStrategy');
        $this->render();
    }

    public function getUsers() {

        $dom_ip = $_POST['dom_ip'];

        //consulta domínio remoto via web service para buscar os usuários do mesmo
        $client = new nusoap_client("http://$dom_ip/".Framework::$systemDirName."/main.php?app=bpm&services&wsdl", array('cache_wsdl' => 0));

        $result = $client->call('getUsers');
        $this->setAction('ajax');
        $this->setLayout('empty');
        $this->setArgsToBody($result);
        $this->render();
    }

    public function replyRequest($input) {

        $this->setAction('replyRequest');

        $request = new request_info();

        $request->loc_id = $input['loc_id'];

        $result = $request->getRequestInfo(TRUE, TRUE, TRUE, TRUE);

        //Framework::debug('result',$result);

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
        $this->render();

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
            Framework::debug('requests',$requests);
            
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


//    public function saveReply($rqt = NULL) {
//        $request = new request_info();
//
//        //REPLY realizado localmente
//        if (!$rqt) {
//            $request->req_id = Common::POST('req_id');
//            $request->dom_src = Common::POST('dom_src');
//
//            $response = Common::POST('response');
//            $message = Common::POST('message');
//
//            $return = $request->updateTo(array('response' => $response, 'message' => $message), FALSE);
//
//            if ($return) {
//
//                $request->response = $response;
//                $request->message = $message;
//
//
//                // $remote = $request->dom_src;
//                $client = new nusoap_client("http://localhost/Framework::$systemDirName/main.php?app=bpm&services&wsdl",array('cache_wsdl' => 0));
//
//                $requestSOAP = array(
//                        'req_id' => $request->req_id,
//                        'dom_src' => $request->dom_src,
//                        'response' => $request->response,
//                        'message' => $request->message);
//
//
//                $result = $client->call('notifyResponse', array($requestSOAP));
//
//                if ($result) {
//                    $this->setFlash( _('Solicitation successfully saved'), 'success');
//                    $this->show();
//                }
//                else {
//                    $this->setFlash( _("Fail to save the solicitation at ").$request->dom_dst, 'error');
//
//                    //rollback no banco local
//                    $request->answer = 'None';
//                    $request->status = 1;
//
//                    if ($request->update())
//                        $this->show();
//                    else {
//                        Framework::debug('probleam with database consistency, please check the requests table');
//                        $this->show();
//                    }
//                }
////                }
////                else { //pedido de requisição é para o mesmo domínio
////                    $this->setFlash( _('Solicitation successfully saved'), 'success');
////                    $this->show();
////                }
//            }
//            else { //não conseguiu adicionar no banco local
//                $this->setFlash( _("Fail to save at local database"), 'error');
//                $this->show();
//            }
//        } else { //requisição remota, via Web Service
//            $request->req_id = $rqt['req_id'];
//            $request->dom_src = $rqt['dom_src'];
//
//            $response = $rqt['response'];
//            $message = $rqt['message'];
//
//            if ($request->updateTo(array('response' => $response, 'message' => $message), FALSE))
//                return TRUE;
//            else return FALSE;
//        }
//    } //function saveReply
//
//
//    function saveRequest($rqt = NULL) {
//        //Teste se o $request está vindo a partir de um Serviço .wsdl ou do próprio formulário
//        unset($request);
//        $request = new request_info();
//
//        //Se a nova requisição é local, busca os dados do formulário preenchidos no new request
//        if ($rqt == NULL) {
//
//            $request->dom_src = Framework::$domIp;
//            $request->req_id = $request->getNextId('req_id');
//            $request->usr_src = AuthSystem::getUserId();
//            $request->dom_dst = $_POST['dom_dst'];
//            $request->urn_src = $_POST['urn_src'];
//            $request->urn_dst = $_POST['urn_dst'];
//            $request->bandwidth = $_POST['bandwidth'];
//
//            //insere no banco local
//            $return = $request->insert();
//
//            if ($return) {
//                $request = $return[0];
//
//                if ($request->dom_dst != $request->dom_src) {
//                    //$remote = $request->dom_dst;
//                    $client = new nusoap_client("http://localhost/Framework::$systemDirName/main.php?app=bpm&services&wsdl",array('cache_wsdl' => 0));
//
//                    $requestSOAP = array(
//                            'req_id' => $request->req_id ,
//                            'dom_src' => $request->dom_src ,
//                            'usr_src' => $request->usr_src,
//                            'dom_dst' => $request->dom_dst,
//                            'urn_src' => $request->urn_src,
//                            'urn_dst' => $request->urn_dst,
//                            'bandwidth' => $request->bandwidth);
//
//
//                    $result = $client->call('', array($requestSOAP));
//
//                    if ($result) {
//                        $this->setFlash( _('Solicitation successfully saved'), 'success');
//                        $this->show();
//                    }
//                    else {
//                        $this->setFlash( _("Fail to save the solicitation at").$request->dom_dst, 'error');
//                        if ($request->delete())
//                            $this->show();
//                        else {
//                            Framework::debug('probleam with database consistency, please check the requests table');
//                            $this->show();
//                        }
//                    }
//                } else { //pedido de requisição é para o mesmo domínio
//                    $this->setFlash( _('Solicitation successfully saved'), 'success');
//                    $this->show();
//                }
//
//            } else { //não conseguiu adicionar no banco local
//                $this->setFlash( _("Fail to save at local database"), 'error');
//                $this->show();
//            }
//        } else { //requisição remota, via Web Service
//
//            $request->req_id = $rqt['req_id'];
//            $request->dom_src = $rqt['dom_src'];
//            $request->usr_src = $rqt['usr_src'];
//            $request->dom_dst = $rqt['dom_dst'];
//
//            $request->urn_src = $rqt['urn_src'];
//            $request->urn_dst = $rqt['urn_dst'];
//            $request->bandwidth = $rqt['bandwidth'];
//
//
//            if ($request->insert())
//                return TRUE;
//            else return FALSE;
//        }
//
//
//    } //do saveRequest
//    

//    function sendRequest($request) {
//
//        if ($request) {
//            //insere no banco local
//            $toinsert = new request_info();
//            $return = $request->insert();
//
//            $client = new nusoap_client("http://localhost:8080/services/ODE",array('cache_wsdl' => 0));
//
//            $requestSOAP = array(
//                    'req_id' => $request->req_id ,
//                    'dom_src' => $request->dom_src ,
//                    'usr_src' => $request->usr_src,
//                    'dom_dst' => $request->dom_dst,
//                    'urn_src' => $request->urn_src,
//                    'urn_dst' => $request->urn_dst,
//                    'bandwidth' => $request->bandwidth);
//
//
//            $result = $client->call('create_reservation', array($requestSOAP));
//
//        }
//    }

//    function notifyRequest($request) {
//
//        if ($request) {
//            //insere no banco local se já nao foi inserido, i.e., se o dom_src != de dom_dst
//            //if ($request->dom_dst != $request->dom_src)
//            Framework::debug('request',$request);
//            $new_request = new request_info();
//            $new_request->dom_src = $request['dom_src'];
//            $new_request->req_id = $request['req_id'];
//            $new_request->usr_src = $request['usr_src'];
//            $new_request->res_id = $request['res_id'];
//            $new_request->dom_dst = Framework::$domIp;
//            //expansao para algo tipo res_details
//
//            if ($new_request->insert()) {
//                return TRUE;
//
//            } else {
//                Framework::debug('fail to save the request by notifyrequest');
//                return FALSE;
//            }
//        } else {
//            Framework::debug('notifyrequest without request set');
//            return FALSE;
//
//        }
//    }



    function notifyResponse($response) {
        Framework::debug('acionando notify response', $response);

        if ($response) {

            $req = new request_info();
            $req->req_id = $response['req_id'];
            $req->setDom('dom_src', $response['dom_src_ip']);
            $req->answerable = 'no';
            
            if ($result = $req->updateTo(array('message' => $response['message'], 'response' => $response['response']), FALSE)) {
                    if ($response['response'] == 'accept') {
                        
                        if ($req->updateTo(array('status' => 'SENT TO OSCARS'), FALSE)){
                            //requisicao aceita deve enviar ao OSCARS
                            Framework::debug('enviando ao OSCARS...');
                            $reservation = $req->fetch(FALSE);
                            $res = new oscars($reservation[0]->resource_id);
                            if ($res->createReservation())
                                return TRUE;
                            else {
                                Framework::debug('erro ao enviar ao oscars');
                                return NULL;
                            }
                        }
                    } else {
                        //requisicao negada, termina
                        Framework::debug('requisicao negada', $response['req_id']);
                        return TRUE;
                    }
            } else {
                //requisicao nao encontrada no banco local, requisicao nao enviada a este dominio
                Framework::debug('req nao encontrada', $response['req_id']);
                return NULL;
            }
        } else {
            Framework::debug('notifyresponse without response set');
            return NULL;
        }
    }
} //class requests

?>
