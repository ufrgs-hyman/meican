<?php

include_once 'libs/Model/resource_model.php';
include_once 'libs/auth.php';

class request_info extends Resource_Model {

    function request_info() {
        $this->setTableName("request_info");

        // Add all table attributes
        $this->addAttribute('loc_id', "INTEGER", true, false, false);
        $this->addAttribute("req_id", "INTEGER");

        $this->addAttribute("src_meican_ip", "VARCHAR");
        $this->addAttribute("src_dom_id", "VARCHAR");
        $this->addAttribute("src_usr", "INTEGER");

        $this->addAttribute("dst_meican_ip", "VARCHAR");
        $this->addAttribute("dst_dom_id", "VARCHAR");

        $this->addAttribute("resource_type", "VARCHAR");
        $this->addAttribute("resource_id", "INTEGER");

        $this->addAttribute('answerable', "VARCHAR");
        $this->addAttribute("status", "VARCHAR");
        $this->addAttribute("response", "VARCHAR");
        $this->addAttribute("message", "VARCHAR");

        $this->addAttribute("crr_meican_ip", "VARCHAR");
        $this->addAttribute("crr_dom_id", "VARCHAR");
        
        $this->addAttribute("response_user", "INTEGER");
        $this->addAttribute("start_time", "FLOAT");
        $this->addAttribute("finish_time", "FLOAT");
    }

    public function setDom($dom_src, $arg_ip) {

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

    public function setDomIp($dom_src_ip, $arg_id) {

        $domain = new domain_info();
        $domain->dom_id = $arg_id;
        if ($result = $domain->fetch(FALSE)) {
            $this->{$dom_src_ip} = $result[0]->dom_ip;
        }
    }

    function getRequestInfo($getReqInfo = false, $getFlowInfo = false, $getTimerInfo = false) {

        //CakeLog::write("debug", "Get request info:\n" . print_r($this, TRUE));

        $meican_info = new meican_info();
        $meican_local = $meican_info->getLocalMeicanIp();
        
        $return_request = new stdClass();

        $return_request->loc_id = $this->loc_id;
        $return_request->req_id = $this->req_id;
        $return_request->status = $this->status;
        $return_request->response = $this->response;
        $return_request->message = $this->message;

        $return_request->resc_descr = _("Unknown");
        $return_request->resc_type = _("Unknown");

        /**
         * Cases:
         *      1) Request is local
         *      1.1) Destination is local
         *      1.2) Destination is remote (WS is required)
         *      2) Request is remote (WS is required)
         *      2.1) Destination is local (final MEICAN)
         *      2.2) Destination is remote (intermediary MEICAN)
         */
        if ($this->src_meican_ip == $meican_local) {
            // Case 1
            // request information is in this MEICAN domain, WS is NOT required
            CakeLog::write("circuits", "Request is local");
            
            $dom_info = new domain_info();
            $dom_info->dom_id = $this->src_dom_id;
            $return_request->src_domain = $dom_info->get("dom_descr", false);

            $user_info = new user_info();
            $user_info->usr_id = $this->src_usr;
            if ($user = $user_info->fetch(false))
                $return_request->src_user = $user[0]->usr_login;
            else
                $return_request->src_user = $this->src_usr;

            if ($this->dst_meican_ip == $meican_local) {
                // Case 1.1
                $dom_info = new domain_info();
                $dom_info->dom_id = $this->dst_dom_id;
            
                $return_request->dst_domain = $dom_info->get("dom_descr", false);
            } else {
                /**
                 *  @TODO try to call a WS to get domain description
                 */
                // Case 1.2
                try {
                    $ODEendpoint = "http://$this->dst_meican_ip}/getMeicanData";
                    $requestSOAP = array('dom_id' => $this->dst_dom_id);

                    $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                    $domain = $client->getDomains($requestSOAP);

                    $return_request->dst_domain = $domain['dom_descr'];
                } catch (Exception $e) {
                    CakeLog::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                    $return_request->dst_domain = $this->dst_meican_ip;
                }
            }

            if ($getReqInfo) {
                $req_tmp = new request_info();
                $req_tmp->req_id = $this->req_id;
                $req_tmp->src_meican_ip = $this->src_meican_ip;
                $req_tmp->src_dom_id = $this->src_dom_id;
                $req_tmp->answerable = 'no';
                
                if ($req_result = $req_tmp->fetch(false)) {
                    $resourceReq = $req_result[0];

                    if ($resourceReq->resource_type == "reservation_info") {
                        $res_info = new reservation_info();
                        $res_info->res_id = $resourceReq->resource_id;
                        if ($reservation = $res_info->fetch(false)) {

                            $return_request->resc_id = $resourceReq->resource_id;
                            $return_request->resc_descr = $reservation[0]->res_name;
                            $return_request->resc_type = $resourceReq->resource_type;

                            $return_request->bandwidth = $reservation[0]->bandwidth;

                            if ($getFlowInfo) {
                                $flow = new flow_info();
                                $flow->flw_id = $reservation[0]->flw_id;
                                $return_request->flow_info = $flow->getFlowDetails();

                                if ($return_request->flow_info->path)
                                    $return_request->flow_info->path = MeicanTopology::getWaypoints($return_request->flow_info->path);

                                $return_request->flow_info->source->domain = $return_request->src_domain;
                                $return_request->flow_info->dest->domain = $return_request->dst_domain;
                            } else
                                $return_request->flow_info = null;

                            if ($getTimerInfo) {
                                $timer = new timer_info();
                                $timer->tmr_id = $reservation[0]->tmr_id;
                                $return_request->timer_info = $timer->getTimerDetails();
                            } else
                                $return_request->timer_info = null;
                        }
                    } else {
                        $resource = new $resourceReq->resource_type();
                        $pk = $resource->getPrimaryKey();
                        $resource->{$pk} = $resourceReq->resource_id;

                        if ($result = $resource->fetch(FALSE)) {
                            $return_request->resc_descr = $result[0]->{$resource->displayField};
                            $return_request->resc_type = $resourceReq->resource_type;
                        }
                        $return_request->flow_info = null;
                        $return_request->timer_info = null;
                    }
                }
            }
        } else {
            // Case 2
            // request information was NOT found in this MEICAN domain, trying WS to get data
            CakeLog::write("circuits", "Request is remote");

            $ODEendpoint = "http://$this->src_meican_ip}/getMeicanData";

            // get source domain
            try {
                $requestSOAP = array('dom_id' => $this->src_dom_id);

                $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                $domain = $client->getDomains($requestSOAP);

                $return_request->src_domain = $domain['dom_descr'];
            } catch (Exception $e) {
                CakeLog::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                $return_request->src_domain = $this->src_meican_ip;
            }

            // get destination domain
            if ($this->dst_meican_ip == $meican_local) {
                // Case 2.1
                $dom_info = new domain_info();
                $dom_info->dom_id = $this->dst_dom_id;
                $return_request->dst_domain = $dom_info->get("dom_descr", false);
            } else {
                // Case 2.2
                try {
                    $dstODEendpoint = "http://$this->dst_meican_ip}/getMeicanData";
                    $requestSOAP = array('dom_id' => $this->dst_dom_id);

                    $client = new SoapClient($dstODEendpoint, array('cache_wsdl' => 0));
                    $domain = $client->getDomains($requestSOAP);

                    $return_request->dst_domain = $domain['dom_descr'];
                } catch (Exception $e) {
                    CakeLog::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                    $return_request->dst_domain = $this->dst_meican_ip;
                }
            }

            //get source user
            try {
                $requestSOAP = array('usr_id' => $this->src_usr);

                $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                $user = $client->getUsers($requestSOAP);

                $return_request->src_user = $user['usr_name'];
            } catch (Exception $e) {
                CakeLog::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
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
                        CakeLog::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                    }

                    if ($getFlowInfo) {
                        try {
                            $requestSOAP = array('res_id' => $this->resource_id);

                            $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                            $flow = $client->getFlowInfo($requestSOAP);

                            $return_request->flow_info = $flow;
                        } catch (Exception $e) {
                            CakeLog::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                        }
                    }

                    if ($getTimerInfo) {
                        try {
                            $requestSOAP = array('res_id' => $this->resource_id);

                            $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                            $timer = $client->getTimerInfo($requestSOAP);

                            $return_request->timer_info = $timer;
                        } catch (Exception $e) {
                            CakeLog::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                        }
                    }
                } else {
                    $return_request->flow_info = null;
                    $return_request->timer_info = null;

                    try {
                        $requestSOAP = array('req_id' => $this->resource_id);

                        $client = new SoapClient($ODEendpoint, array('cache_wsdl' => 0));
                        $request = $client->getRequestInfo($requestSOAP);

                        $return_request->resc_descr = $request['resc_descr'];
                    } catch (Exception $e) {
                        CakeLog::write("error", "Caught exception while trying to call getMeicanData from ODE: " . print_r($e->getMessage()));
                    }
                }
            }
        }
        CakeLog::write("debug", "Request info return:\n" . print_r($return_request, true));

        return $return_request;
    }

    public function response() {
        $meican = new meican_info();
        $meican_local = $meican->getLocalMeicanIp();

        $message = $this->message;
        $response = $this->response;

        $this->message = "";
        $this->response = "";

        $now = microtime(true);
        $usr = AuthSystem::getUserId();

        $res = $this->fetch(false);
        $toResponse = $res[0];

        if ($toResponse->crr_meican_ip == $meican_local) {
            if (!$toResponse->response) {
                //$local = $this->updateTo(array('response' => $response, 'message' => $message, 'status' => 'ANSWERED', 'finish_time' => $now, 'response_user' => $usr), FALSE);

                $responseSOAP = array(
                    'req_id' => $toResponse->req_id,
                    'src_meican_ip' => $toResponse->src_meican_ip,
                    'src_dom_id' => $toResponse->src_dom_id,
                    'response' => $response,
                    'message' => $message);

                $dom = new domain_info();
                $dom->dom_id = $toResponse->crr_dom_ip;
                $res_domain = $dom->fetch(false);

                $domain = $res_domain[0];

                if ($domain->ode_wsdl_path && $domain->ode_response) {

                    CakeLog::write("circuits", "Sending response:\n" . print_r($responseSOAP, true));

                    try {
                        $client = new SoapClient($domain->ode_wsdl_path, array('cache_wsdl' => WSDL_CACHE_NONE));

                        if ($client->{$domain->ode_response}($responseSOAP)) {
                            $local = $this->updateTo(array('finish_time' => $now, 'response_user' => $usr), false);
                        }
                        //$client->__soapCall($domain->ode_response, $responseSOAP);
                        // wait for 2 seconds while ODE call saveResponse, then it will update de DB
                        sleep(2);

                        return true;
                    } catch (Exception $e) {
                        CakeLog::write("error", "Caught exception while trying to connect to ODE:\n" . print_r($e->getMessage()));
                        return false;
                    }
                } else {
                    CakeLog::write("error", 'ODE not confired correctly');
                    return false;
                }
            } else {
                CakeLog::write('warning', "Request already answered:\n" . print_r($this, true));
                return false;
            }
        } else {
            CakeLog::write('warning', "MEICAN not current:\n" . print_r($this, true));
            return false;
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
