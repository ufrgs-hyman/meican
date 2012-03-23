<?php

include_once 'libs/Model/resource_model.php';
include_once 'libs/auth.php';

class request_info extends Resource_Model {

    function request_info() {
        $this->setTableName("request_info");

        // Add all table attributes
        $this->addAttribute('loc_id', "INTEGER", true, false, false);
        $this->addAttribute("req_id","INTEGER");
        
        $this->addAttribute("src_ode_ip","VARCHAR");
        $this->addAttribute("src_usr","INTEGER");
        
        $this->addAttribute("dst_ode_ip","VARCHAR");
        
        $this->addAttribute("resource_type","VARCHAR");
        $this->addAttribute("resource_id","INTEGER");
        
        $this->addAttribute('answerable',"VARCHAR");
        $this->addAttribute("status","VARCHAR");
        $this->addAttribute("response","VARCHAR");
        $this->addAttribute("message","VARCHAR");
        
        $this->addAttribute("response_user", "INTEGER");
        $this->addAttribute("start_time", "FLOAT");
        $this->addAttribute("finish_time", "FLOAT");
    }

    public function setDom($dom_src, $arg_ip){

        $domain = new domain_info();
        $domain->dom_ip = $arg_ip;
        if ($result = $domain->fetch(FALSE)) {
            $this->{$dom_src} = $result[0]->dom_id;
        } else {
            //domain nao existe ira adicionar
            $obj = $domain->insert();
            $this->{$dom_src} = $obj->dom_id;
        }

    }

    public function setDomIp($dom_src_ip, $arg_id){

        $domain = new domain_info();
        $domain->dom_id = $arg_id;
        if ($result = $domain->fetch(FALSE)) {
            $this->{$dom_src_ip} = $result[0]->dom_ip;
        }

    }


//    public function getRequestInfo() {
//
//
//
//        if ($allowPks) {
//            $inString = implode(',',$allowPks);
//            $pk = 'loc_id';
//            $aclString = "$pk IN ($inString)";
//
//
//            $sql = "select loc_id, req_id, d.dom_descr as dom_src, d.dom_ip as dom_src_ip, usr_src, d2.dom_descr as dom_dst, d2.dom_ip as dom_dst_ip, res_id, response, message
//            from request_info as r
//            left join domain_info as d on r.dom_src=d.dom_id
//            left join domain_info as d2 on d2.dom_id=r.dom_dst
//            WHERE
//
//            $where = $this->buildWhere();
//
//            if ($this->dom_src)
//                $sql .= "AND d.dom_ip = '$this->dom_src'";//dom_src_ip
//            elseif ($this->dom_dst)
//                $sql .= "AND d2.dom_ip = '$this->dom_dst'";//dom_dst_ip
//
//            return $this->querySql($sql);
//        } else return FALSE; //sem acesso a nada
//
//    }

//    public function send(){
//
//        if ($this->res_id) {
//            $tmp = new request_info();
//            $tmp->dom_src = ;
//            $tmp->req_id = $tmp->getNextId('req_id');
//
//            $this->dom_src = ;
//            $this->req_id = $tmp->req_id;
//            $this->usr_src = AuthSystem::getUserId();
//            $ode_ip = ;
//
//            if (parent::insert()) {
//                debug('ira enviar...');
//                $endpoint="http://$ode_ip/ode/deployment/bundles/jj-10/processes/jj.10/processes.ode/diagrama-odeJJ.wsdl";
//
//                $client = new SoapClient($endpoint,array('cache_wsdl' => 0));
//
//                 $requestSOAP = array(
//                    'req_id' => $this->req_id ,
//                    'dom_source' => $this->dom_src ,
//                    'user_source' => $this->usr_src,
//                    // 'dom_dst' => $this->dom_dst,
//                    'res_id' => $this->res_id);
//
//                $result = $client->RecebeRequisicao($requestSOAP);
//
//                if ($result) {
//                    debug('enviada');
//                    return TRUE;
//
//                } else {
//                    debug('fail to send to ode');
//                        return FALSE;
//                }
//
//            } else {
//                debug('fail to add at local database');
//                return FALSE;
//            }
//
//        } else {
//            debug('falta setar res_id');
//            return FALSE;
//        }
//
//
//    }

    function getRequestInfo($getReqInfo = FALSE, $getFlowInfo = FALSE, $getTimerInfo = FALSE) {

        $domain_info = new domain_info();
        $domain_info->ode_ip = $this->src_ode_ip;

        $return_request = new stdClass();

        $return_request->response = $this->response;
        $return_request->message = $this->message;
        
        $return_request->resc_descr = _("Unknown");
        $return_request->resc_type = _("Unknown");

        if ($domain_result = $domain_info->fetch(FALSE)) {
            // request information was found in this MEICAN domain, WS is NOT required

            $return_request->src_domain = $domain_result[0]->dom_descr;

            $user_info = new user_info();
            $user_info->usr_id = $this->src_usr;
            if ($user = $user_info->fetch(FALSE))
                $return_request->src_user = $user[0]->usr_name;
            else
                $return_request->src_user = $this->src_usr;

            $domain_info = new domain_info();
            $domain_info->ode_ip = $this->dst_ode_ip;
            if ($dst_domain = $domain_info->fetch(FALSE))
                $return_request->dst_domain = $dst_domain[0]->dom_descr;
            else {
                // try to call a WS to get domain description
                try {
                    $ODEendpoint = "http://$this->src_ode_ip}/getMeicanData";
                    $requestSOAP = array('ode_ip' => $this->dst_ode_ip);

                    $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                    $domain = $client->getDomains($requestSOAP);

                    $return_request->dst_domain = $domain['dom_descr'];
                } catch (Exception $e) {
                    Log::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));

                    $return_request->dst_domain = $this->dst_ode_ip;
                }
            }

            if ($getReqInfo) {
                if ($this->resource_type == "reservation_info") {
                    $res_info = new reservation_info();
                    $res_info->res_id = $this->resource_id;
                    $reservation = $res_info->fetch(FALSE);

                    $return_request->resc_descr = $reservation[0]->res_name;
                    $return_request->resc_type = $this->resource_type;
                    
                    $return_request->bandwidth = $reservation[0]->bandwidth;

                    if ($getFlowInfo) {
                        $flow = new flow_info();
                        $flow->flw_id = $reservation[0]->flw_id;
                        $return_request->flow_info = (array) $flow->getFlowDetails();
                    } else
                        $return_request->flow_info = NULL;

                    if ($getTimerInfo) {
                        $timer = new timer_info();
                        $timer->tmr_id = $reservation[0]->tmr_id;
                        $return_request->timer_info = (array) $timer->getTimerDetails();
                    } else
                        $return_request->timer_info = NULL;
                } else {
                    $resource = new $this->resource_type();
                    $pk = $resource->getPrimaryKey();
                    $resource->{$pk} = $this->resource_id;

                    if ($result = $resource->fetch(FALSE)) {
                        $return_request->resc_descr = $result[0]->{$resource->displayField};
                        $return_request->resc_type = $this->resource_type;
                    }
                    $return_request->flow_info = NULL;
                    $return_request->timer_info = NULL;
                }
            }
        } else {
            // request information was NOT found in this MEICAN domain, trying WS to get data

            $ODEendpoint = "http://$this->src_ode_ip}/getMeicanData";

            // get source domain
            try {
                $requestSOAP = array('ode_ip' => $this->src_ode_ip);

                $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                $domain = $client->getDomains($requestSOAP);

                $return_request->src_domain = $domain['dom_descr'];
            } catch (Exception $e) {
                Log::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));

                $return_request->src_domain = $this->src_ode_ip;
            }

            // get destination domain
            try {
                $requestSOAP = array('ode_ip' => $this->dst_ode_ip);

                $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                $domain = $client->getDomains($requestSOAP);

                $return_request->dst_domain = $domain['dom_descr'];
            } catch (Exception $e) {
                Log::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));

                $return_request->dst_domain = $this->dst_ode_ip;
            }

            //get source user
            try {
                $requestSOAP = array('usr_id' => $this->src_usr);

                $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                $user = $client->getUsers($requestSOAP);

                $return_request->src_user = $user['usr_name'];
            } catch (Exception $e) {
                Log::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));

                $return_request->src_user = $this->src_usr;
            }

            // get request info
            if ($getReqInfo) {
                $return_request->resc_type = $this->resource_type;

                if ($this->resource_type == "reservation_info") {
                    try {
                        $requestSOAP = array('res_id' => $this->resource_id);

                        $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                        $reservation = $client->getReservations($requestSOAP);

                        $return_request->resc_descr = $reservation['res_name'];
                    } catch (Exception $e) {
                        Log::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                    }

                    if ($getFlowInfo) {
                        try {
                            $requestSOAP = array('res_id' => $this->resource_id);

                            $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                            $flow = $client->getFlowInfo($requestSOAP);

                            $return_request->flow_info = $flow;
                        } catch (Exception $e) {
                            Log::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                        }
                    }

                    if ($getTimerInfo) {
                        try {
                            $requestSOAP = array('res_id' => $this->resource_id);

                            $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                            $timer = $client->getTimerInfo($requestSOAP);

                            $return_request->timer_info = $timer;
                        } catch (Exception $e) {
                            Log::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                        }
                    }
                } else {
                    $return_request->flow_info = NULL;
                    $return_request->timer_info = NULL;

                    try {
                        $requestSOAP = array('req_id' => $this->resource_id);

                        $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                        $request = $client->getRequestInfo($requestSOAP);

                        $return_request->resc_descr = $request['resc_descr'];
                    } catch (Exception $e) {
                        Log::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                    }
                }
            }
        }

        return $return_request;
    }
    
    /**
     *
     * response do meican2706
     * 
     * 
     
     public function response() {
        $message = $this->message;
        $response = $this->response;

        unset($this->message);
        unset($this->response);

        $now = microtime(true);
        $usr = AuthSystem::getUserLogin();

        $res = $this->fetch(FALSE);
        $tmp = $res[0];

        if (!$tmp->response) {

            $local = $this->updateTo(array('response' => $response, 'message' => $message, 'status' => 'ANSWERED', 'finish_time' => $now, 'response_user' => $usr), FALSE);

            if ($local) {

                $result = $this->fetch(FALSE);
                $toSend = $result[0];
                $toSend->setDomIp('dom_src_ip', $toSend->dom_src);
                $endpoint = Framework::$odeWsdl;

                try {
                    $client = new SoapClient($endpoint, array('cache_wsdl' => 0));

                    $responseSOAP = array(
                        'req_id' => $toSend->req_id,
                        'dom_src_ip' => $toSend->dom_src_ip,
                        'response' => $toSend->response,
                        'message' => $toSend->message);
                    $service = Framework::$serviceToResponse;
                    $client->$service($responseSOAP);

                    return TRUE;
                } catch (Exception $e) {
                    Framework::debug('fail to send to ode');
                    return FALSE;
                }
            } else {
                Framework::debug('fail to add at local database');
                return FALSE;
            }
        } else {
            Framework::debug('request already answered');
            return FALSE;
        }
    }
     
     */

    public function response() {
        $message = $this->message;
        $response = $this->response;

        unset($this->message);
        unset($this->response);

        $local = $this->updateTo(array('response' => $response, 'message' => $message, 'status' => 'ANSWERED'), FALSE);

        if ($local) {

            $result = $this->fetch(FALSE);
            $toSend = $result[0];
            $toSend->setDomIp('dom_src_ip', $toSend->dom_src);
            $endpoint = Configure::read('odeWSDLToResponse');
            //$endpoint = "http://".odip."/ode/deployment/bundles/v1_strategy1_pietro/processes/v1_strategy1_pietro/processes.ode/diagrama-v1_strategy1_pietro.wsdl";
            try {
                $client = new SoapClient($endpoint, array('cache_wsdl' => 0));

                $responseSOAP = array(
                        'req_id' => $toSend->req_id,
                        'dom_src_ip' => $toSend->dom_src_ip,
                        'response' => $toSend->response,
                        'message' => $toSend->message);

                $client->ReceiveResponse($responseSOAP);

                return TRUE;

            } catch (Exception $e) {
                debug('fail to send to ode');
                return FALSE;
            }

        } else {
            debug('fail to add at local database');
            return FALSE;
        }
    }

    public function checkRequests() {
        $noReq = 0;
        $result = $this->fetch();
        foreach ($result as $t) {
               if ($t->answerable == 'yes')
                if (!$t->response)
                    $noReq++;
        }
        return $noReq;
    }
}

?>
