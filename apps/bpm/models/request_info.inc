<?php

include_once 'libs/resource_model.inc';
include_once 'includes/auth.inc';

class request_info extends Resource_Model {

    function request_info() {

        $this->setTableName("request_info");

        // Add all table attributes
        $this->addAttribute('loc_id', "INTEGER", true, false, false);
        $this->addAttribute("req_id","INTEGER");
        $this->addAttribute("src_dom","INTEGER");
        $this->addAttribute("dst_dom","INTEGER");
        $this->addAttribute("src_usr","INTEGER");
        $this->addAttribute("resource_type","VARCHAR");
        $this->addAttribute("resource_id","INTEGER");
        $this->addAttribute('answerable',"VARCHAR");
        $this->addAttribute("status","VARCHAR");
        $this->addAttribute("response","VARCHAR");
        $this->addAttribute("message","VARCHAR");

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
//            $tmp->dom_src = Framework::$domIp;
//            $tmp->req_id = $tmp->getNextId('req_id');
//
//            $this->dom_src = Framework::$domIp;
//            $this->req_id = $tmp->req_id;
//            $this->usr_src = AuthSystem::getUserId();
//            $ode_ip = Framework::$odeIp;
//
//            if (parent::insert()) {
//                Framework::debug('ira enviar...');
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
//                    Framework::debug('enviada');
//                    return TRUE;
//
//                } else {
//                    Framework::debug('fail to send to ode');
//                        return FALSE;
//                }
//
//            } else {
//                Framework::debug('fail to add at local database');
//                return FALSE;
//            }
//
//        } else {
//            Framework::debug('falta setar res_id');
//            return FALSE;
//        }
//
//
//    }

    function getRequestInfo($getResInfo = FALSE, $getFlowInfo = FALSE, $getTimerInfo = FALSE,$getEndpointsInfo = FALSE) {
        $temp = $this->fetch();

        if ($temp) {
            foreach ($temp as $t) {
                $domain = new domain_info();
                $domain->dom_id = $t->dom_src;
                $result2 = $domain->fetch(FALSE);
                if ($result2[0]->dom_descr)
                    $t->dom_src = $result2[0]->dom_descr;
                else $t->dom_src = $result2[0]->dom_ip;

                $domain2 = new domain_info();
                $domain2->dom_id = $t->dom_dst;
                $result3 = $domain2->fetch(FALSE);
                if ($result3[0]->dom_descr)
                    $t->dom_dst = $result3[0]->dom_descr;
                else  $t->dom_dst = $result3[0]->dom_ip;

                $endpoint =  "http://{$result2[0]->dom_ip}/".Framework::$systemDirName."/main.php?app=aaa&services&wsdl";
                $ws_client = new nusoap_client($endpoint, array('cache_wsdl' => 0));

                $usr = array('usr_id' => $t->usr_src);

                if ($result = $ws_client->call('getUsers', array('usr' => $usr)))
                    $t->usr_src = $result[0]['usr_name'];

                if ($getResInfo) { //busca nome da reserva
                    $endpoint2 =  "http://{$result2[0]->dom_ip}/".Framework::$systemDirName."/main.php?app=bpm&services&wsdl";
                    $ws_client2 = new nusoap_client($endpoint2, array('cache_wsdl' => 0));

                    if ($result = $ws_client2->call('getReqInfo',array('req_id' => $t->req_id))){
                        $t->resc_id = $result['resc_id'];
                        $t->resc_descr = $result['resc_descr'];
                        $t->resc_type = $result['resc_type'];
                    } else {
                          $t->resc_descr = _('Unknown');
                          $t->resc_type = _('Unknown');
                    }

                 $endpoint_circuits =  "http://{$result2[0]->dom_ip}/".Framework::$systemDirName."/main.php?app=circuits&services&wsdl";
                 $ws_client_circuits = new nusoap_client($endpoint_circuits, array('cache_wsdl' => 0));

                    if ($getTimerInfo) { //busca informacoes de timer
                        if ($result = $ws_client_circuits->call('getTimerInfo',array($t->resc_id)))
                            $t->timer_info = $result;
                        else $t->timer_info = _('Unknown');
                    }


                    if ($getFlowInfo) { //buscar informacoes de flow: urns, dom_ips, bandwidth
                       
                        $t->flow_info = $ws_client_circuits->call('getFlowInfo',array('res_id' => $t->resc_id));

                        if ($getEndpointsInfo) { //busca net_descr, dev_descr e port_number
                            $endpoint3 =  "http://{$t->flow_info['src_dom_ip']}/".Framework::$systemDirName."/main.php?app=topology&services&wsdl";
                            $urn_array = array($t->flow_info['src_urn_string']);
                            if ($ws_client3 = new nusoap_client($endpoint3, array('cache_wsdl' => 0)))
                                if ($u_src = $ws_client3->call('getURNsInfo', array('urn_string_list' => $urn_array)))
                                    $t->src_endpoint = $u_src[0];

                            $endpoint4 =  "http://{$t->flow_info['dst_dom_ip']}./".Framework::$systemDirName."/main.php?app=topology&services&wsdl";
                            $urn_array = array($t->flow_info['dst_urn_string']);
                            if ($ws_client4 = new nusoap_client($endpoint4, array('cache_wsdl' => 0)))
                                if ($u_dst = $ws_client3->call('getURNsInfo', array('urn_string_list' => $urn_array)))
                                    $t->dst_endpoint = $u_dst[0];
                        }
                    }
                }
            }
            
            return $temp;
        }
        return FALSE;
    }

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
            $endpoint = Framework::$odeWSDLToResponse;
            //$endpoint = "http://".Framework::$odeIp."/ode/deployment/bundles/v1_strategy1_pietro/processes/v1_strategy1_pietro/processes.ode/diagrama-v1_strategy1_pietro.wsdl";
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
                Framework::debug('fail to send to ode');
                return FALSE;
            }

        } else {
            Framework::debug('fail to add at local database');
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
