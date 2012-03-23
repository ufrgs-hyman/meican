<?php

include_once 'libs/controller.php';
require_once 'libs/nuSOAP/lib/nusoap.php';

include_once 'apps/aaa/models/user_info.php';
include_once 'apps/aaa/models/group_info.php';
include_once 'apps/bpm/models/request_info.php';
include_once 'apps/circuits/models/reservation_info.php';
include_once 'apps/circuits/models/flow_info.php';
include_once 'apps/circuits/models/timer_info.php';
include_once 'apps/topology/models/topology.php';
include_once 'apps/topology/models/meican_info.php';

class ws extends Controller {

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

        /*$server->wsdl->addComplexType('stringTypeList', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'xsd:string[]'),
            'xsd:string'));*/

        $server->wsdl->addComplexType('reqType', 'complexType', 'struct', 'all', '', array(
            'resc_id' => array('name' => 'resc_id', 'type' => 'xsd:int'),
            'resc_descr' => array('name' => 'resc_descr', 'type' => 'xsd:string'),
            'resc_type' => array('name' => 'resc_type', 'type' => 'xsd:string')));

        $server->wsdl->addComplexType('requestType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_ode_ip' => array('name' => 'src_ode_ip', 'type' => 'xsd:string'),
            'dst_ode_ip' => array('name' => 'dst_ode_ip', 'type' => 'xsd:string'),
            'src_usr' => array('name' => 'src_usr', 'type' => 'xsd:int')));

        $server->wsdl->addComplexType('responseType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_ode_ip' => array('name' => 'src_ode_ip', 'type' => 'xsd:string'),
            'response' => array('name' => 'response', 'type' => 'xsd:string'),
            'message' => array('name' => 'message', 'type' => 'xsd:string')));

        $server->register(
                'getReqInfo', array('req_id' => 'xsd:int', 'src_ode_ip' => 'xsd:string'), array('req_info' => 'tns:reqType'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/getReqInfo", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'notifyResponse', array('response' => 'tns:responseType'), array('return' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/notifyResponse", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'requestUserAuthorization', array('usr_dst' => 'xsd:int', 'request' => 'tns:requestType'), array('req_id' => 'xsd:int'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/requestUserAuthorization", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'requestGroupAuthorization', array('grp_dst' => 'xsd:int', 'request' => 'tns:requestType'), array('req_id' => 'xsd:int'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/requestGroupAuthorization", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'getRequestPath', array('req_id' => 'xsd:int'), array('ode_ip_array' => 'tns:stringTypeList'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/getRequestPath", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'refreshRequestStatus', array('req_id' => 'xsd:int', 'src_ode_ip' => 'xsd:string', 'new_status' => 'xsd:string'), array('confirmation' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/refreshRequestStatus", 'rpc', 'encoded', 'Complex Hello World Method');

        
        function getReqInfo($req_id, $src_ode_ip) {
            Log::write('ws', "Get request info from ODE:" . print_r(array('req_id' => $req_id, 'src_ode_ip' => $src_ode_ip), TRUE));

            $req = new request_info();
            $req->req_id = $req_id;
            $req->src_ode_ip = $src_ode_ip;
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

        function notifyResponse($response) {
            Log::write('ws', "Notify response from ODE:\n" . print_r($response, TRUE));
            $validResponses = array("accept", "reject");

            if (array_search($response['response'], $validResponses) !== FALSE) {

                $req = new request_info();
                $req->req_id = $response['req_id'];
                $req->src_ode_ip = $response['src_ode_ip'];

                if (!$req->get('response', FALSE)) {

                    $req->updateTo(array('message' => $response['message'], 'response' => $response['response']), FALSE);

                    if ($response['response'] == 'accept') {
                        // request accepted
                        $req->updateTo(array("status" => "AUTHORIZED"), FALSE);

                        /**
                         * @todo Transformar em função de algum modelo (reservation_info ou gri_info)
                         */
                        $tmp = new gri_info();
                        $tmp->res_id = $req->get('resource_id', FALSE);
                        $allgris = $tmp->fetch(FALSE);

                        Log::write('ws', "Notify response: reservation authorized, setting GRIs to be sent. Reservation ID:\n" . print_r($tmp->res_id, TRUE));

                        foreach ($allgris as $g) {
                            $now = time();
                            $start = new DateTime($g->start);
                            if ($now < ($start->getTimestamp() - 180)) //testa para ver se a reserva está NO MINIMO 3 minutos do tempo atual
                                $g->updateTo(array("send" => 1), FALSE);

//                            se for uma recorrencia, nao pode colocar timed out
//                            else
//                                $g->updateTo(array("status" => "TIMED OUT"), FALSE);
                        }
                    } else {
                        // request denied
                        $req->updateTo(array("status" => "DENIED"), FALSE);

                        //as reservas devem ser canceladas no OSCARS
                        $tmp = new gri_info();
                        $tmp->res_id = $req->get('resource_id', FALSE);
                        $allgris = $tmp->fetch(FALSE);

                        $dom_tmp = new domain_info();
                        $dom_tmp->ode_ip = $req->src_ode_ip;
                        $dom = $dom_tmp->fetch(FALSE);

                        foreach ($allgris as $g) {
                            $oscRes = new OSCARSReservation();
                            $oscRes->setOscarsUrl($dom->oscars_ip);
                            $oscRes->setGri($g->gri_descr);
                            if ($oscRes->cancelReservation()) {
                                //apaga os gris negados do db MEICAN
                                $g->delete(FALSE);
                            } else {
                                Log::write('ws', "Notify response: error to cancel GRI:\n" . print_r($g->gri_descr, TRUE));
                            }
                            unset($oscRes);
                        }
                    }

                    return TRUE; //se a requisicao foi negada ou aceita retorna true
                } else {
                    Log::write('ws', "Notify response: request already answered");
                    return NULL;
                }
            } else {
                Log::write('ws', "Notify response: invalid response from ODE:\n" . print_r($response, TRUE));
                return NULL;
            }
        }

        function requestUserAuthorization($usr_dst, $request) {
            Log::write('ws', "Request user authorizarion:\n" . print_r($request, TRUE));

            if ($usr_dst && $request) {

                $new_request = new request_info();
                $new_request->req_id = $request['req_id'];

                $new_request->src_ode_ip = $request['src_ode_ip'];
                $new_request->src_usr = $request['src_usr'];
                $new_request->dst_ode_ip = $request['dst_ode_ip'];
                
                $new_request->resource_type = NULL;
                $new_request->resource_id = NULL;
                
                $new_request->answerable = 'yes';
                
                $new_request->status = NULL;
                $new_request->response = NULL;
                $new_request->message = NULL;
                
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
            Log::write('ws', 'Request group authorizarion' . print_r($request, TRUE));

            if ($grp_dst && $request) {
                $new_request = new request_info();
                $new_request->req_id = $request['req_id'];

                $new_request->src_ode_ip = $request['src_ode_ip'];
                $new_request->src_usr = $request['src_usr'];
                $new_request->dst_ode_ip = $request['dst_ode_ip'];
                
                $new_request->resource_type = NULL;
                $new_request->resource_id = NULL;
                
                $new_request->answerable = 'yes';
                
                $new_request->status = NULL;
                $new_request->response = NULL;
                $new_request->message = NULL;
                
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

        function getRequestPath($req_id) {
            Log::write('ws', "Getting request path:\n" . print_r(array('req_id' => $req_id), TRUE));
            
            $req_info = new request_info();
            $req_info->req_id = $req_id;
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
                        //$businessEndpoint = "http://$src_dom->ode_ip/$src_dom->ode_wsdl_path";
                    } else
                        $ode_ip_array[] = NULL;
                }
            }
            Log::write('ws', "Request path return with ODE IPs:\n" . print_r($ode_ip_array, TRUE));

            return $ode_ip_array;
        }

        function refreshRequestStatus($req_id, $src_ode_ip, $new_status) {
            Log::write('ws', 'Refresh request status:' . print_r(array('req_id' => $req_id, 'src_ode_ip' => $src_ode_ip, 'status' => $new_status), TRUE));

            $req = new request_info();
            $req->req_id = $req_id;
            $req->src_ode_ip = $src_ode_ip;

            if ($new_status && $req->fetch(FALSE)) {
                if ($req->updateTo(array('status' => $new_status), FALSE))
                    return TRUE;
            }
            return NULL;
        }

        $POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        $server->service($POST_DATA);
    }

}

?>
