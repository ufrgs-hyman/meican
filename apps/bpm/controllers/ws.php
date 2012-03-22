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
        /* $server->wsdl->addComplexType('stringTypeList','complexType','array','all','',
          array('str' => array('name' => 'str','type' => 'xsd:string')));
         * 
         */

        $server->wsdl->addComplexType('stringTypeList', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'xsd:string[]'),
            'xsd:string'));

        $server->wsdl->addComplexType('reqType', 'complexType', 'struct', 'all', '', array(
            'resc_id' => array('name' => 'resc_id', 'type' => 'xsd:int'),
            'resc_descr' => array('name' => 'resc_descr', 'type' => 'xsd:string'),
            'resc_type' => array('name' => 'resc_type', 'type' => 'xsd:string')));

        $server->wsdl->addComplexType('requestType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'dom_src_ip' => array('name' => 'dom_src_ip', 'type' => 'xsd:string'),
            'dom_dst_ip' => array('name' => 'dom_dst_ip', 'type' => 'xsd:string'),
            'usr_src' => array('name' => 'usr_src', 'type' => 'xsd:int')));

        $server->wsdl->addComplexType('responseType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'dom_src_ip' => array('name' => 'dom_src_ip', 'type' => 'xsd:string'),
            'response' => array('name' => 'response', 'type' => 'xsd:string'),
            'message' => array('name' => 'message', 'type' => 'xsd:string')));

        $server->register(
                'getReqInfo', array('req_id' => 'xsd:int', 'dom_src_ip' => 'xsd:string'), array('req_info' => 'tns:reqType'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/getReqInfo", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'notifyResponse', array('name' => 'tns:responseType'), array('return' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/notifyResponse", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'requestUserAuthorization', array('usr_dst' => 'xsd:int', 'request' => 'tns:requestType'), array('req_id' => 'xsd:int'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/requestUserAuthorization", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'requestGroupAuthorization', array('grp_dst' => 'xsd:int', 'request' => 'tns:requestType'), array('req_id' => 'xsd:int'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/requestGroupAuthorization", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'getRequestPath', array('req_id' => 'xsd:int'), array('ode_ip_array' => 'tns:stringTypeList'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/getRequestPath", 'rpc', 'encoded', 'Complex Hello World Method');

        $server->register(
                'refreshRequestStatus', array('req_id' => 'xsd:int', 'dom_src_ip' => 'xsd:string', 'new_status' => 'xsd:string'), array('confirmation' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name/$this->app/ws/refreshRequestStatus", 'rpc', 'encoded', 'Complex Hello World Method');

        
        function getReqInfo($req_id, $src_ode_ip) {
            Log::write("info", "Get request info from ODE:" . print_r(array('req_id' => $req_id, 'src_ode_ip' => $src_ode_ip), TRUE));

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
                    $return = array('resc_id' => $resource->{$pk},
                        'resc_descr' => $result2[0]->displayField,
                        'resc_type' => $rescTy);

                    Log::write('info', 'Request info return:' . print_r($return, TRUE));
                    return $return;
                }
            }
            return NULL;
        }

        function notifyResponse($response) {
            Log::write("info", "Notify response from ODE" . print_r($response, TRUE));
            $validResponses = array("accept", "reject");

            if (array_search($response['response'], $validResponses)) {

                $req = new request_info();
                $req->req_id = $response['req_id'];
                $req->src_ode_ip = $response['src_ode_ip'];
                $req->answerable = 'no';

                if (!$req->get('response')) {

                    $req->updateTo(array('message' => $response['message'], 'response' => $response['response']), FALSE);

                    if ($response['response'] == 'accept') {
                        // request accepted
                        $req->updateTo(array("status" => "AUTHORIZED"), FALSE);

                        /**
                         * @todo Transformar em função de algum modelo (reservation_info ou gri_info)
                         */
                        $tmp = new gri_info();
                        $tmp->res_id = $req->get('resource_id');
                        $allgris = $tmp->fetch(FALSE);

                        Log::write("info", "Notify response: reservation authorized, setting GRIs to be sent. Reservation ID:" . print_r($tmp->res_id, TRUE));

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
                        $tmp->res_id = $req->get('resource_id');
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
                                Log::write("error", "Notify response: error to cancel GRI" . print_r($g->gri_descr, TRUE));
                            }
                            unset($oscRes);
                        }
                    }

                    return TRUE; //se a requisicao foi negada ou aceita retorna true
                } else {
                    Log::write("notice", "Notify response: request already answered");
                    return NULL;
                }
            } else {
                Log::write("error", "Notify response: invalid response from ODE" . print_r($response, TRUE));
                return NULL;
            }
        }

        function requestUserAuthorization($usr_dst, $request) {
            Log::write('info', 'Request user authorizarion' . print_r($request, TRUE));

            if ($usr_dst && $request) {

                $new_request = new request_info();
                $new_request->req_id = $request['req_id'];

                $new_request->src_ode_ip = $request['src_ode_ip'];
                $new_request->src_usr = $request['src_usr'];
                $new_request->dst_ode_ip = $request['dst_ode_ip'];

                $new_request->answerable = 'yes';
                $new_request->start_time = microtime(true);

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
                        Log::write('error', 'Fail to save the request by requestUserAuthorization');
                        return NULL;
                    }
                } else {
                    Log::write('error', 'Destination user not found by requestUserAuthorization');
                    return NULL;
                }
            } else {
                Log::write('error', 'Not enough arguments in requestUserAuthorization');
                return NULL;
            }
        }

        function requestGroupAuthorization($grp_dst, $request) {
            Log::write('info', 'Request group authorizarion' . print_r($request, TRUE));

            if ($grp_dst && $request) {
                $new_request = new request_info();
                $new_request->req_id = $request['req_id'];

                $new_request->src_ode_ip = $request['src_ode_ip'];
                $new_request->src_usr = $request['src_usr'];
                $new_request->dst_ode_ip = $request['dst_ode_ip'];

                $new_request->answerable = 'yes';
                $new_request->start_time = microtime(true);

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
                        Log::write('error', 'Fail to save the request by requestGroupAuthorization');
                        return NULL;
                    }
                } else {
                    Log::write('error', 'Destination group not found by requestGroupAuthorization');
                    return NULL;
                }
            } else {
                Log::write('error', 'Not enough arguments in requestGroupAuthorization');
                return NULL;
            }
        }

        function getRequestPath($req_id) {
            $gri = "gri";
            $oscars_url = "http";

            $req = new request_info();
            $req->req_id = $req_id;
            $req->answerable = 'no';
            $req->fetch(FALSE);
            
            if ($req->resource_type == "reservation_info") {
                $reservation = new reservation_info();
                $reservation->res_id = $req->resource_id;
                $pathArray = $reservation->getPath();
            }
            
            $ode_ip_array = array();
                    // put only the topology IDs into an array
                    $topoIdArray = array();
                    $dom = new domain_info();
                    foreach ($pathArray as $urn) {
                        $topoIdArray[] = $dom->getTopologyId($urn);
                    }
                    array_unique($topoIdArray);

                    // fill the array with the Endpoints of the path
                    foreach ($topoIdArray as $topId) {
                        $dom = new domain_info();
                        $dom->topology_id = $topId;
                        if ($d_result = $dom->fetch(FALSE)) {
                            $d_tmp = $d_result[0];
                            $ode_ip_array[] = $d_tmp->ode_ip;
                            //$businessEndpoint = "http://$src_dom->ode_ip/$src_dom->ode_wsdl_path";
                        }
                    }            
            
            return $ode_ip_array;
        }

        function refreshRequestStatus($req_id, $src_ode_ip, $new_status) {
            Log::write('info', 'Refresh request status:' . print_r(array('req_id' => $req_id, 'src_ode_ip' => $src_ode_ip, 'status' => $new_status), TRUE));

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
