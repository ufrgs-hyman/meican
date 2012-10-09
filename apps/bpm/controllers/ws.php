<?php

include_once 'libs/controller.php';
require_once 'libs/Vendors/nuSOAP/lib/nusoap.php';

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
        $this_dir_name = ($this_dir_name) ? "$this_dir_name/" : "";
        
        $this->meican_local = $this_ip;

        $namespace = "http://MEICAN";
        $server = new nusoap_server();
        $server->configureWSDL("MEICAN_BPM_SERVICES", $namespace, "http://$this_ip/$this_dir_name$this->app/ws");
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
            'src_meican_ip' => array('name' => 'src_meican_ip', 'type' => 'xsd:string'),
            'src_topology_id' => array('name' => 'src_topology_id', 'type' => 'xsd:string'),
            'dst_meican_ip' => array('name' => 'dst_meican_ip', 'type' => 'xsd:string'),
            'dst_topology_id' => array('name' => 'dst_topology_id', 'type' => 'xsd:string'),
            'crr_meican_ip' => array('name' => 'crr_meican_ip', 'type' => 'xsd:string'),
            'crr_topology_id' => array('name' => 'crr_topology_id', 'type' => 'xsd:string'),
            'src_usr' => array('name' => 'src_usr', 'type' => 'xsd:int')));
        
        $server->wsdl->addComplexType('statusType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_meican_ip' => array('name' => 'src_meican_ip', 'type' => 'xsd:string'),
            'src_topology_id' => array('name' => 'src_topology_id', 'type' => 'xsd:string'),
            'status' => array('name' => 'status', 'type' => 'xsd:string')));
        
        $server->wsdl->addComplexType('responseType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_meican_ip' => array('name' => 'src_meican_ip', 'type' => 'xsd:string'),
            'src_topology_id' => array('name' => 'src_topology_id', 'type' => 'xsd:string'),
            'crr_meican_ip' => array('name' => 'crr_meican_ip', 'type' => 'xsd:string'),
            'crr_topology_id' => array('name' => 'crr_topology_id', 'type' => 'xsd:string'),
            'response' => array('name' => 'response', 'type' => 'xsd:string'),
            'message' => array('name' => 'message', 'type' => 'xsd:string')));

        $server->wsdl->addComplexType('decisionType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_meican_ip' => array('name' => 'src_meican_ip', 'type' => 'xsd:string'),
            'src_topology_id' => array('name' => 'src_topology_id', 'type' => 'xsd:string'),
            'response' => array('name' => 'response', 'type' => 'xsd:string')));
        
        $server->wsdl->addComplexType('primaryType', 'complexType', 'struct', 'all', '', array(
            'req_id' => array('name' => 'req_id', 'type' => 'xsd:int'),
            'src_meican_ip' => array('name' => 'src_meican_ip', 'type' => 'xsd:string'),
            'src_topology_id' => array('name' => 'src_topology_id', 'type' => 'xsd:string'),
            'crr_meican_ip' => array('name' => 'crr_meican_ip', 'type' => 'xsd:string'),
            'crr_topology_id' => array('name' => 'crr_topology_id', 'type' => 'xsd:string')));
        

        $server->register(
                'getReqInfo', array('req_id' => 'xsd:int', 'src_meican_ip' => 'xsd:string', 'src_topology_id' => 'xsd:string'), array('req_info' => 'tns:reqType'), $namespace, "http://$this_ip/$this_dir_name$this->app/ws/getReqInfo", 'rpc', 'encoded', 'Method to get request information');

        $server->register(
                'refreshStatus', array('status' => 'tns:statusType'), array('return' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name$this->app/ws/refreshStatus", 'rpc', 'encoded', 'Method only to refresh request status, modify all requests');
        
        $server->register(
                'saveResponse', array('response' => 'tns:responseType'), array('return' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name$this->app/ws/saveResponse", 'rpc', 'encoded', 'Method to save response from current domain request, modify only one request');
        
        $server->register(
                'finalDecision', array('decision' => 'tns:decisionType'), array('return' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name$this->app/ws/finalDecision", 'rpc', 'encoded', 'Method to notify final response to the user\'s request, it saves the response to the resource request and modifies all the request status as AUTHORIZED or DENIED');

        $server->register(
                'requestUserAuthorization', array('usr_dst' => 'xsd:int', 'request' => 'tns:requestType'), array('req_id' => 'xsd:int'), $namespace, "http://$this_ip/$this_dir_name$this->app/ws/requestUserAuthorization", 'rpc', 'encoded', 'Method to request authorization from a specific user');

        $server->register(
                'requestGroupAuthorization', array('grp_dst' => 'xsd:int', 'request' => 'tns:requestType'), array('req_id' => 'xsd:int'), $namespace, "http://$this_ip/$this_dir_name$this->app/ws/requestGroupAuthorization", 'rpc', 'encoded', 'Method to request authorization from a group');

        $server->register(
                'getNextDomain', array('primary' => 'tns:primaryType'), array('next_domain' => 'xsd:string'), $namespace, "http://$this_ip/$this_dir_name$this->app/ws/getNextDomain", 'rpc', 'encoded', 'Complex Hello World Method');
        
        $server->register(
                'getRequestPath', array('req_id' => 'xsd:int', 'src_meican_ip' => 'xsd:string', 'src_topology_id' => 'xsd:string'), array('topo_id_array' => 'tns:stringTypeList'), $namespace, "http://$this_ip/$this_dir_name$this->app/ws/getRequestPath", 'rpc', 'encoded', 'Method to get the reservation path');

        
        function getReqInfo($req_id, $src_meican_ip, $src_topology_id) {
            CakeLog::write('ws', "Get request info from ODE:" . print_r(array('req_id' => $req_id, 'src_meican_ip' => $src_meican_ip, 'src_topology_id' => $src_topology_id), true));

            $req = new request_info();
            $req->req_id = $req_id;
            $req->src_meican_ip = trim($src_meican_ip);
            $req->src_topology_id = trim($src_topology_id);
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

                    CakeLog::write('ws', 'Request info return:' . print_r($return, true));
                    return $return;
                }
            }
            return null;
        }
        
        function refreshStatus($status) {
            CakeLog::write('ws', "Refresh status:\n" . print_r($status, true));

            if (array_key_exists('req_id', $status) &&
                    array_key_exists('src_meican_ip', $status) &&
                    array_key_exists('src_topology_id', $status) &&
                    array_key_exists('status', $status)) {

                if ($status['req_id'] && $status['src_meican_ip'] && $status['src_topology_id'] && $status['status']) {

                    $req = new request_info();
                    $req->req_id = $status['req_id'];
                    $req->src_meican_ip = trim($status['src_meican_ip']);
                    $req->src_topology_id = trim($status['src_topology_id']);

                    if ($req->updateTo(array("status" => $status['status']), false)) {
                        CakeLog::write('ws', "Refresh status: request status updated");
                        return true;
                    }
                }
            }
            CakeLog::write('ws', "Refresh status: could not update status");
            return null;
        }
        
        function saveResponse($response) {
            CakeLog::write('ws', "Save response:\n" . print_r($response, true));

            if (array_key_exists('req_id', $response) &&
                    array_key_exists('src_meican_ip', $response) &&
                    array_key_exists('src_topology_id', $response) &&
                    array_key_exists('crr_meican_ip', $response) &&
                    array_key_exists('crr_topology_id', $response) &&
                    array_key_exists('response', $response) &&
                    array_key_exists('message', $response)) {

                if ($response['req_id'] && $response['src_meican_ip'] && $response['src_topology_id'] && $response['crr_meican_ip'] && $response['crr_topology_id']) {

                    $req = new request_info();
                    $req->req_id = $response['req_id'];
                    $req->src_meican_ip = trim($response['src_meican_ip']);
                    $req->src_topology_id = trim($response['src_topology_id']);
                    $req->crr_meican_ip = trim($response['crr_meican_ip']);
                    $req->crr_topology_id = trim($response['crr_topology_id']);

                    $validResponses = array("accept", "reject");

                    if (array_search($response['response'], $validResponses) !== false) {
                        if (!$req->get('response', false)) {
                            $updated = false;
                            if ($response['message'])
                                $updated = $req->updateTo(array("response" => $response['response'], "message" => $response['message']), false);
                            else
                                $updated = $req->updateTo(array("response" => $response['response']), false);

                            if ($updated) {
                                CakeLog::write('ws', "Save response: request response saved");
                                return true;
                            }
                        } else
                            CakeLog::write('ws', "Save response: request already answered");
                    } else
                        CakeLog::write('ws', "Save response: invalid response");
                }
            }
            CakeLog::write('ws', "Save response: request response NOT saved");
            return null;
        }

        function finalDecision($decision) {
            CakeLog::write('ws', "Final decision from ODE:\n" . print_r($decision, true));

            if (array_key_exists('req_id', $decision) &&
                    array_key_exists('src_meican_ip', $decision) &&
                    array_key_exists('src_topology_id', $decision) &&
                    array_key_exists('response', $decision)) {

                if ($decision['req_id'] && $decision['src_meican_ip'] && $decision['src_topology_id']) {

                    $validResponses = array("accept", "reject");

                    if (array_search($decision['response'], $validResponses) !== false) {

                        $req = new request_info();
                        $req->req_id = $decision['req_id'];
                        $req->src_meican_ip = trim($decision['src_meican_ip']);
                        $req->src_topology_id = trim($decision['src_topology_id']);
                        
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
                            CakeLog::write('ws', "Final decision: reservation authorized, setting GRIs to be sent. Reservation ID: " . print_r($tmp->res_id, true));

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

                            CakeLog::write('ws', "Final decision: reservation denied, cancelling GRIs. Reservation ID:\n" . print_r($tmp->res_id, true));

                            $dom_tmp = new domain_info();
                            $dom_tmp->topology_id = $req->src_topology_id;
                            $dom = $dom_tmp->fetch(false);

                            //as reservas devem ser canceladas no OSCARS
                            foreach ($allgris as $g) {
                                $oscRes = new OSCARSReservation();
                                $oscRes->setOscarsUrl($dom[0]->idc_url);
                                $oscRes->setGri($g->gri_descr);
                                if ($oscRes->cancelReservation()) {
                                    CakeLog::write('ws', "Final decision: GRI cancelled:\n" . print_r($g->gri_descr, true));
                                    //apaga os gris negados do db MEICAN
                                    //$g->delete(false);
                                } else {
                                    CakeLog::write('ws', "Final decision: error to cancel GRI:\n" . print_r($g->gri_descr, true));
                                }
                                unset($oscRes);
                            }
                        }

                        return true; //se a requisicao foi negada ou aceita retorna true
                    } else
                        CakeLog::write('ws', "Final decision: invalid response from ODE:\n" . print_r($decision, true));
                }
            }
            return null;
        }
        
        function requestUserAuthorization($usr_dst, $request) {
            CakeLog::write('ws', "Request user authorizarion:\nUser: ". print_r($usr_dst, true). "\n" . print_r($request, true));

            if ($usr_dst && $request) {
                $new_request = new request_info();
                $new_request->fillRequest($request);

                //insere embaixo do usuario passado como parametro
                $user = new user_info();
                $user->usr_id = $usr_dst;
                $resuser = $user->fetch(false);

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

                        return true;
                    } else {
                        CakeLog::write('ws', 'Fail to save the request by requestUserAuthorization');
                        return null;
                    }
                } else {
                    CakeLog::write('ws', 'Destination user not found by requestUserAuthorization');
                    return null;
                }
            } else {
                CakeLog::write('ws', 'Not enough arguments in requestUserAuthorization');
                return null;
            }
        }

        function requestGroupAuthorization($grp_dst, $request) {
            CakeLog::write('ws', "Request group authorizarion:\nGroup: ". print_r($grp_dst, true). "\n" . print_r($request, true));

            if ($grp_dst && $request) {
                $new_request = new request_info();
                $new_request->fillRequest($request);

                //insere embaixo do grupo passado como parametro
                $group = new group_info();
                $group->grp_id = $grp_dst;
                $resgroup = $group->fetch(false);

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

                        return true;
                    } else {
                        CakeLog::write('ws', 'Fail to save the request by requestGroupAuthorization');
                        return null;
                    }
                } else {
                    CakeLog::write('ws', 'Destination group not found by requestGroupAuthorization');
                    return null;
                }
            } else {
                CakeLog::write('ws', 'Not enough arguments in requestGroupAuthorization');
                return null;
            }
        }
        
        function getNextDomain($primary) {
            CakeLog::write('ws', "Getting next domain:\n" . print_r($primary, true));

            if (array_key_exists('req_id', $primary) &&
                    array_key_exists('src_meican_ip', $primary) &&
                    array_key_exists('src_topology_id', $primary) &&
                    array_key_exists('crr_meican_ip', $primary) &&
                    array_key_exists('crr_topology_id', $primary)) {

                if ($this->meican_local == trim($primary['crr_meican_ip'])) {

                    $topo_id_array = getRequestPath($primary['req_id'], $primary['src_meican_ip'], $primary['src_topology_id']);

                    $next_domain = null;
                    $crr_domain = trim($primary['crr_topology_id']);

                    for ($index = 0; $index < count($topo_id_array); $index++) {
                        if ($topo_id_array[$index] == $crr_domain) {
                            if (array_key_exists($index + 1, $topo_id_array)) {
                                $next_domain = $topo_id_array[$index + 1];
                                break;
                            }
                        }
                    }
                    CakeLog::write('ws', "Next domain:\n" . print_r(array('next_domain' => $next_domain), true));
                    return $next_domain;
                } else {
                    CakeLog::write('ws', "MEICAN is not current:\n" . print_r(array('meican_local' => $this->meican_local, 'crr_meican_ip' => $primary['crr_meican_ip']), true));
                    return null;
                }
            }
        }
        
        function getRequestPath($req_id, $src_meican_ip, $src_topology_id) {
            CakeLog::write('ws', "Getting request path:\n" . print_r(array('req_id' => $req_id, 'src_meican_ip' => $src_meican_ip, 'src_topology_id' => $src_topology_id), true));
            
            $req_info = new request_info();
            $req_info->req_id = $req_id;
            $req_info->src_meican_ip = trim($src_meican_ip);
            $req_info->src_topology_id = trim($src_topology_id);
            $req_info->answerable = 'no';
            $request = $req_info->fetch(false);

            $topoIdArray = array();
            if ($request[0]->resource_type == "reservation_info") {
                $reservation = new reservation_info();
                $reservation->res_id = $request[0]->resource_id;
                $pathArray = $reservation->getPath();

                // put only the topology IDs into an array
                $dom = new domain_info();
                foreach ($pathArray as $urn) {
                    $topoIdArray[] = $dom->getTopologyId($urn);
                }
                $topoIdArray = array_unique($topoIdArray);

                // fill the array preceded by MEICAN IP, e.g.: meican.cipo.rnp.br:cipo.inf.ufrgs.br
//                foreach ($topoIdArray as $index => $topoId) {
//                    $topoIdArray[$index] = $this->meican_local.":$topoId";
//                }
            }
            CakeLog::write('ws', "Request path return with topology IDs:\n" . print_r($topoIdArray, true));

            return $topoIdArray;
        }

        $POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        $server->service($POST_DATA);
    }

}

?>
