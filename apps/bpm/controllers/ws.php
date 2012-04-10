<?php

include_once 'libs/controller.php';
require_once 'libs/nuSOAP/lib/nusoap.php';

include_once 'apps/aaa/models/user_info.php';
include_once 'apps/aaa/models/group_info.php';
include_once 'apps/bpm/models/request_info.php';
include_once 'apps/circuits/models/reservation_info.php';
include_once 'apps/topology/models/meican_info.php';
include_once LIBS.'webservice_controller.php';

class ws extends WebServiceController {

    public function ws() {
        $this->app = 'bpm';
        $this->controller = 'ws';
        $this->defaultAction = '';

        $this_meican = new meican_info();
        $this_ip = $this_meican->getLocalMeicanIp();
        $this_dir_name = $this_meican->getLocalMeicanDirName();

        $namespace = "http://MEICAN";
        $server = new nusoap_server();
        $server->configureWSDL("MEICAN_BPM_SERVICES", $namespace, "http://$this_ip/$this_dir_name/$this->app/ws");
        //$server->wsdl->schemaTargetNamespace = $namespace;

        /**
         * Os tipos array abaixo definidos não funcionam adequadamente quando passados como parâmetro de entrada. No caso de
         * servirem como retorno funcionam adequadamente.
         * Ao utilizar o outro formato de definição de tipo complexo array com SOAP-ENC, outros aplicativos como o Soap UI e
         * o ODE não conseguem funcionar.
         * O problema é na declaração no namespace. Devendo ser encontrado alguma forma para declarar o SOAP-ENC nas definições
         * do WSDL
         */
        
        
        $server->wsdl->addComplexType('stringTypeList','complexType','array','all','',
          array('str' => array('name' => 'str','type' => 'xsd:string')));

        $server->wsdl->addComplexType('reqType', 'complexType', 'struct', 'all', '', array(
            'resc_id' => array('name' => 'resc_id', 'type' => 'xsd:int'),
            'resc_descr' => array('name' => 'resc_descr', 'type' => 'xsd:string'),
            'resc_type' => array('name' => 'resc_type', 'type' => 'xsd:string')));

        $server->wsdl->addComplexType('requestType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_ode_ip' => array('name' => 'src_ode_ip', 'type' => 'xsd:string'),
            'dst_ode_ip' => array('name' => 'dst_ode_ip', 'type' => 'xsd:string'),
            'crr_ode_ip' => array('name' => 'crr_ode_ip', 'type' => 'xsd:string'),
            'src_usr' => array('name' => 'src_usr', 'type' => 'xsd:int')));
        
        $server->wsdl->addComplexType('statusType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_ode_ip' => array('name' => 'src_ode_ip', 'type' => 'xsd:string'),
            'status' => array('name' => 'status', 'type' => 'xsd:string')));
        
        $server->wsdl->addComplexType('responseType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_ode_ip' => array('name' => 'src_ode_ip', 'type' => 'xsd:string'),
            'crr_ode_ip' => array('name' => 'crr_ode_ip', 'type' => 'xsd:string'),
            'response' => array('name' => 'response', 'type' => 'xsd:string'),
            'message' => array('name' => 'message', 'type' => 'xsd:string')));

        $server->wsdl->addComplexType('decisionType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_ode_ip' => array('name' => 'src_ode_ip', 'type' => 'xsd:string'),
            'response' => array('name' => 'response', 'type' => 'xsd:string')));
        
        $server->wsdl->addComplexType('primaryType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_ode_ip' => array('name' => 'src_ode_ip', 'type' => 'xsd:string'),
            'crr_ode_ip' => array('name' => 'crr_ode_ip', 'type' => 'xsd:string')));
        

        $server->register(
                'getReqInfo', array('req_id' => 'xsd:int', 'src_ode_ip' => 'xsd:string'), array('req_info' => 'tns:reqType'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/getReqInfo", 'rpc', 'encoded', 'Method to get request information');

        $server->register(
                'refreshStatus', array('status' => 'tns:statusType'), array('return' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/refreshStatus", 'rpc', 'encoded', 'Method only to refresh request status, modify all requests');
        
        $server->register(
                'saveResponse', array('response' => 'tns:responseType'), array('return' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/saveResponse", 'rpc', 'encoded', 'Method to save response from current domain request, modify only one request');
        
        $server->register(
                'finalDecision', array('decision' => 'tns:decisionType'), array('return' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/finalDecision", 'rpc', 'encoded', 'Method to notify final response to the user\'s request, it saves the response to the resource request and modifies all the request status as AUTHORIZED or DENIED');

        $server->register(
                'requestUserAuthorization', array('usr_dst' => 'xsd:int', 'request' => 'tns:requestType'), array('req_id' => 'xsd:int'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/requestUserAuthorization", 'rpc', 'encoded', 'Method to request authorization from a specific user');

        $server->register(
                'requestGroupAuthorization', array('grp_dst' => 'xsd:int', 'request' => 'tns:requestType'), array('req_id' => 'xsd:int'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/requestGroupAuthorization", 'rpc', 'encoded', 'Method to request authorization from a group');

        $server->register(
                'getNextDomain', array('primary' => 'tns:primaryType'), array('next_domain' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/getNextDomain", 'rpc', 'encoded', 'Complex Hello World Method');
        
        $server->register(
                'getRequestPath', array('req_id' => 'xsd:int', 'src_ode_ip' => 'xsd:string'), array('ode_ip_array' => 'tns:stringTypeList'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/getRequestPath", 'rpc', 'encoded', 'Method to get the reservation path');

        
        function getReqInfo($req_id, $src_ode_ip) {
            Log::write('ws', "Get request info from ODE:" . print_r(array('req_id' => $req_id, 'src_ode_ip' => $src_ode_ip), TRUE));

            $req = new request_info();
            $req->req_id = $req_id;
            $req->src_ode_ip = trim($src_ode_ip);
            $req->answerable = 'no';

            if ($result = $req->fetch(FALSE)) {
                $rescTy = $result[0]->resource_type;

                $resource = new $rescTy();
                $pk = $resource->getPrimaryKey();
                $resource->{$pk} = $result[0]->resource_id;

                if ($result2 = $resource->fetch(FALSE)) {
                    $return = array(
                        'resc_id' => $resource->{$pk},
                        'resc_type' => $rescTy,
                        'resc_descr' => $result2[0]->{$resource->displayField});

                    Log::write('ws', 'Request info return:' . print_r($return, TRUE));
                    return $return;
                }
            }
            return NULL;
        }
        
        function refreshStatus($status) {
            Log::write('ws', "Refresh status:\n" . print_r($status, true));

            if (array_key_exists('req_id', $status) &&
                    array_key_exists('src_ode_ip', $status) &&
                    array_key_exists('status', $status)) {

                if ($status['req_id'] && $status['src_ode_ip'] && $status['status']) {

                    $req = new request_info();
                    $req->req_id = $status['req_id'];
                    $req->src_ode_ip = trim($status['src_ode_ip']);

                    if ($req->updateTo(array("status" => $status['status']), false)) {
                        Log::write('ws', "Refresh status: request status updated");
                        return true;
                    }
                }
            }
            Log::write('ws', "Refresh status: could not update status");
            return null;
        }
        
        function saveResponse($response) {
            Log::write('ws', "Save response:\n" . print_r($response, true));

            if (array_key_exists('req_id', $response) &&
                    array_key_exists('src_ode_ip', $response) &&
                    array_key_exists('crr_ode_ip', $response) &&
                    array_key_exists('response', $response) &&
                    array_key_exists('message', $response)) {

                if ($response['req_id'] && $response['src_ode_ip'] && $response['crr_ode_ip']) {

                    $req = new request_info();
                    $req->req_id = $response['req_id'];
                    $req->src_ode_ip = trim($response['src_ode_ip']);
                    $req->crr_ode_ip = trim($response['crr_ode_ip']);

                    $validResponses = array("accept", "reject");

                    if (array_search($response['response'], $validResponses) !== false) {
                        if (!$req->get('response', false)) {
                            $updated = false;
                            if ($response['message'])
                                $updated = $req->updateTo(array("response" => $response['response'], "message" => $response['message']), false);
                            else
                                $updated = $req->updateTo(array("response" => $response['response']), false);

                            if ($updated) {
                                Log::write('ws', "Save response: request response saved");
                                return true;
                            }
                        } else
                            Log::write('ws', "Save response: request already answered");
                    } else
                        Log::write('ws', "Save response: invalid response");
                }
            }
            Log::write('ws', "Save response: request response NOT saved");
            return null;
        }

        function finalDecision($decision) {
            Log::write('ws', "Final decision from ODE:\n" . print_r($decision, true));

            if (array_key_exists('req_id', $decision) &&
                    array_key_exists('src_ode_ip', $decision) &&
                    array_key_exists('response', $decision)) {

                if ($decision['req_id'] && $decision['src_ode_ip']) {

                    $validResponses = array("accept", "reject");

                    if (array_search($decision['response'], $validResponses) !== false) {

                        $req = new request_info();
                        $req->req_id = $decision['req_id'];
                        $req->src_ode_ip = trim($decision['src_ode_ip']);
                        
                        $req->answerable = 'no';

                        // save final response to the resource request
                        $req->updateTo(array("response" => $decision['response']), false);

                        $tmp = new gri_info();
                        $tmp->res_id = $req->get('resource_id', false);
                        $allgris = $tmp->fetch(false);

                        $req->answerable = '';

                        if ($decision['response'] == 'accept') {
                            // request accepted
                            $req->updateTo(array("status" => "AUTHORIZED"), false);
                            
                            /**
                             * @todo Transformar em função de algum modelo (reservation_info ou gri_info)
                             */
                            Log::write('ws', "Final decision: reservation authorized, setting GRIs to be sent. Reservation ID: " . print_r($tmp->res_id, true));

                            foreach ($allgris as $g) {
                                $now = time();
                                $start = new DateTime($g->start);
                                if ($now < ($start->getTimestamp() - 180)) //testa para ver se a reserva está NO MINIMO 3 minutos do tempo atual
                                    $g->updateTo(array("send" => 1), false);

//                            se for uma recorrencia, nao pode colocar timed out
//                            else
//                                $g->updateTo(array("status" => "TIMED OUT"), FALSE);
                            }
                        } else {
                            // request denied
                            $req->updateTo(array("status" => "DENIED"), false);

                            Log::write('ws', "Final decision: reservation denied, cancelling GRIs. Reservation ID:\n" . print_r($tmp->res_id, true));

                            $dom_tmp = new domain_info();
                            $dom_tmp->ode_ip = $req->src_ode_ip;
                            $dom = $dom_tmp->fetch(false);

                            //as reservas devem ser canceladas no OSCARS
                            foreach ($allgris as $g) {
                                $oscRes = new OSCARSReservation();
                                $oscRes->setOscarsUrl($dom[0]->oscars_ip);
                                $oscRes->setGri($g->gri_descr);
                                if ($oscRes->cancelReservation()) {
                                    //apaga os gris negados do db MEICAN
                                    $g->delete(false);
                                } else {
                                    Log::write('ws', "Final decision: error to cancel GRI:\n" . print_r($g->gri_descr, true));
                                }
                                unset($oscRes);
                            }
                        }

                        return true; //se a requisicao foi negada ou aceita retorna true
                    } else
                        Log::write('ws', "Final decision: invalid response from ODE:\n" . print_r($decision, true));
                }
            }
            return null;
        }

        function requestUserAuthorization($usr_dst, $request) {
            Log::write('ws', "Request user authorizarion:\nUser: ". print_r($usr_dst, TRUE). "\n" . print_r($request, TRUE));

            if ($usr_dst && $request) {

                $new_request = new request_info();
                $new_request->req_id = $request['req_id'];

                $new_request->src_ode_ip = trim($request['src_ode_ip']);
                $new_request->src_usr = $request['src_usr'];
                $new_request->dst_ode_ip = trim($request['dst_ode_ip']);
                
                $new_request->resource_type = NULL;
                $new_request->resource_id = NULL;
                
                $new_request->answerable = 'yes';
                
                $new_request->status = NULL;
                $new_request->response = NULL;
                $new_request->message = NULL;
                
                $new_request->crr_ode_ip = trim($request['crr_ode_ip']);
                $new_request->response_user = NULL;
                $new_request->start_time = microtime(true);
                $new_request->finish_time = NULL;

                //insere embaixo do usuario passado como parametro
                $user = new user_info();
                $user->usr_id = $usr_dst;
                $resuser = $user->fetch(FALSE);

                if ($resuser) {
                    if ($new_request->insert($usr_dst, 'user_info')) {

                        /* if ($resuser[0]->usr_email) {
                          // manda e-mail de notificação para o usuário
                          $to = $resuser[0]->usr_email;

                          $dom = new domain_info();
                          $ldom = $dom->getLocalDomain();

                          $text = _("You have one new request to be answered. Please log in.");
                          $text .= _("\n\nDomain:")." $ldom->dom_descr";
                          $text .= _("\nSystem URL:")." http://".Framework::$domIp."/".Framework::$systemDirName;

                          $subject = _("New request from MEICAN");

                          $headers = array();
                          $headers["To"] = $resuser[0]->usr_name . ' <' . $resuser[0]->usr_email . '>';

                          $mail = new Meican_Mail();
                          $mail->send($to, $text, $subject, $headers);
                          } */

                        return TRUE;
                    } else {
                        Log::write('ws', 'Fail to save the request by requestUserAuthorization');
                        return NULL;
                    }
                } else {
                    Log::write('ws', 'Destination user not found by requestUserAuthorization');
                    return NULL;
                }
            } else {
                Log::write('ws', 'Not enough arguments in requestUserAuthorization');
                return NULL;
            }
        }

        function requestGroupAuthorization($grp_dst, $request) {
            Log::write('ws', "Request group authorizarion:\nGroup: ". print_r($grp_dst, TRUE). "\n" . print_r($request, TRUE));

            if ($grp_dst && $request) {
                $new_request = new request_info();
                $new_request->req_id = $request['req_id'];

                $new_request->src_ode_ip = trim($request['src_ode_ip']);
                $new_request->src_usr = $request['src_usr'];
                $new_request->dst_ode_ip = trim($request['dst_ode_ip']);
                
                $new_request->resource_type = NULL;
                $new_request->resource_id = NULL;
                
                $new_request->answerable = 'yes';
                
                $new_request->status = NULL;
                $new_request->response = NULL;
                $new_request->message = NULL;
                
                $new_request->crr_ode_ip = trim($request['crr_ode_ip']);
                $new_request->response_user = NULL;
                $new_request->start_time = microtime(true);
                $new_request->finish_time = NULL;
                

                //insere embaixo do grupo passado como parametro
                $group = new group_info();
                $group->grp_id = $grp_dst;
                $resgroup = $group->fetch(FALSE);

                if ($resgroup) {
                    if ($insertedReq = $new_request->insert($grp_dst, 'group_info')) {

                        /*
                          // manda e-mail de notificação para o grupo
                          if ($users = $resgroup[0]->fetchUsers()) {

                          $recipients = array();
                          $headers = array();
                          $headers["To"] = $resgroup[0]->grp_descr;
                          foreach ($users as $u) {
                          if ($u->usr_email) {
                          $recipients[] = $u->usr_email;
                          }
                          }

                          $dom = new domain_info();
                          $ldom = $dom->getLocalDomain();

                          $text = _("Your group") . " '" . $resgroup[0]->grp_descr . "' " . _("has one new request to be answered. Please log in.");
                          $text .= _("\n\nDomain:") . " $ldom->dom_descr";
                          $text .= _("\nSystem URL:") . " http://" . Framework::$domIp . "/" . Framework::$systemDirName;

                          $subject = _("New request from MEICAN");

                          if ($recipients) {
                          $mail = new Meican_Mail();
                          $mail->send($recipients, $text, $subject, $headers);
                          }
                          }
                         */

                        return TRUE;
                    } else {
                        Log::write('ws', 'Fail to save the request by requestGroupAuthorization');
                        return NULL;
                    }
                } else {
                    Log::write('ws', 'Destination group not found by requestGroupAuthorization');
                    return NULL;
                }
            } else {
                Log::write('ws', 'Not enough arguments in requestGroupAuthorization');
                return NULL;
            }
        }
        
        function getNextDomain($primary) {
            Log::write('ws', "Getting next domain:\n" . print_r($primary, TRUE));

            if (array_key_exists('req_id', $primary) &&
                    array_key_exists('src_ode_ip', $primary) &&
                    array_key_exists('crr_ode_ip', $primary)) {

                $ode_ip_array = getRequestPath($primary['req_id'], $primary['src_ode_ip']);

                $next_domain = NULL;

                for ($index = 0; $index < count($ode_ip_array); $index++) {
                    if ($ode_ip_array[$index] == trim($primary['crr_ode_ip'])) {
                        if (array_key_exists($index + 1, $ode_ip_array)) {
                            $next_domain = $ode_ip_array[$index + 1];
                            break;
                        }
                    }
                }
                Log::write('ws', "Next domain:\n" . print_r(array('next_domain' => $next_domain), TRUE));
                return $next_domain;
            }
        }
        
        function getRequestPath($req_id, $src_ode_ip) {
            Log::write('ws', "Getting request path:\n" . print_r(array('req_id' => $req_id, 'src_ode_ip' => $src_ode_ip), TRUE));
            
            $req_info = new request_info();
            $req_info->req_id = $req_id;
            $req_info->src_ode_ip = trim($src_ode_ip);
            $req_info->answerable = 'no';
            $request = $req_info->fetch(FALSE);

            $ode_ip_array = array();
            if ($request[0]->resource_type == "reservation_info") {
                $reservation = new reservation_info();
                $reservation->res_id = $request[0]->resource_id;
                $pathArray = $reservation->getPath();

                // put only the topology IDs into an array
                $topoIdArray = array();
                $dom = new domain_info();
                foreach ($pathArray as $urn) {
                    $topoIdArray[] = $dom->getTopologyId($urn);
                }
                $topoIdArray = array_unique($topoIdArray);

                // fill the array with the ODE IPs of the path
                foreach ($topoIdArray as $topId) {
                    $dom = new domain_info();
                    $dom->topology_id = $topId;
                    if ($d_result = $dom->fetch(FALSE)) {
                        $d_tmp = $d_result[0];
                        $ode_ip_array[] = $d_tmp->ode_ip;
                    }
                }
            }
            Log::write('ws', "Request path return with ODE IPs:\n" . print_r($ode_ip_array, TRUE));

            return $ode_ip_array;
        }

        $POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        $server->service($POST_DATA);
    }

}

?>
