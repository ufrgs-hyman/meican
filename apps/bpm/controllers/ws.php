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

        $server->wsdl->addComplexType('userType','complexType','struct','all','',
                array('usr_id' => array('name' => 'usr_id','type' => 'xsd:int'),
                'usr_name' => array('name' => 'usr_name','type' => 'xsd:string')));

        $server->wsdl->addComplexType('userTypeList','complexType','array','all','',
                array('usr' => array('name' => 'usr','type' => 'tns:groupType')));

        $server->wsdl->addComplexType('groupType','complexType','struct','all','',
                array('grp_id' => array('name' => 'grp_id','type' => 'xsd:int'),
                'grp_descr' => array('name' => 'grp_descr','type' => 'xsd:string')));

        $server->wsdl->addComplexType('groupTypeList','complexType','array','all','',
                array('grp' => array('name' => 'grp','type' => 'tns:groupType')));

        $server->wsdl->addComplexType('stringTypeList','complexType','array','all','',
                array('str' => array('name' => 'str','type' => 'xsd:string')));

        $server->wsdl->addComplexType('reqType','complexType','struct','all','',
                array('resc_id' => array('name' => 'resc_id','type' => 'xsd:int'),
                'resc_descr' => array('name' => 'resc_descr','type' => 'xsd:string'),
                'resc_type' => array('name' => 'resc_type','type' => 'xsd:string')));

        $server->wsdl->addComplexType('flowType','complexType','struct','all','',
                array(
                'src_dom_ip' => array('name' => 'src_domain_ip', 'type' => 'xsd:string'),
                'src_urn_string' => array('name' => 'src_urn_string', 'type' => 'xsd:string'),
                'dst_dom_ip' => array('name' => 'dst_domain_ip', 'type' => 'xsd:string'),
                'dst_urn_string' => array('name' => 'dst_urn_string', 'type' => 'xsd:string'),
                'bandwidth' => array('name' => 'bandwidth', 'type' => 'xsd:int')));

        $server->wsdl->addComplexType('timerType','complexType','struct','all','',
                array(
                'start' => array('name' => 'start', 'type' => 'xsd:date'),
                'finish' => array('name' => 'finish', 'type' => 'xsd:date'),
                'recurrence' => array('name' => 'recurrence', 'type' => 'xsd:string')));

        $server->wsdl->addComplexType('urnType','complexType','struct','all','',
                array(
                'net_descr' => array('name' => 'net_descr', 'type' => 'xsd:string'),
                'dev_descr' => array('name' => 'dev_descr', 'type' => 'xsd:string'),
                'port_number' => array('name' => 'port_number', 'type' => 'xsd:int')));

        $server->wsdl->addComplexType('requestType','complexType','struct','all','',
                array(
                'req_id' => array('name' => 'req_id','type' => 'xsd:int'),
                'dom_src_ip' => array('name' => 'dom_src_ip','type' => 'xsd:string'),
                'dom_dst_ip' => array('name' => 'dom_dst_ip', 'type' => 'xsd:string'),
                'usr_src' => array('name' => 'usr_src','type' => 'xsd:int')));

        $server->wsdl->addComplexType('responseType','complexType','struct','all','',
                array(
                'req_id' => array('name' => 'req_id','type' => 'xsd:int'),
                'dom_src_ip' => array('name' => 'dom_src_ip','type' => 'xsd:string'),
                'response' => array('name' => 'response','type' => 'xsd:string'),
                'message' => array('name' => 'message','type' => 'xsd:string')));

        /**
         *  Tipos definidos para uso na função geturndetails
         **/
        $server->wsdl->addComplexType('portType','complexType','struct','all','',
                array('port_number'  => array('name' => 'port_number','type' => 'xsd:int'),
                'vlan'         => array('name' => 'vlan','type' => 'xsd:string'),
                'max_capacity' => array('name' => 'max_capacity','type' => 'xsd:int'),
                'min_capacity' => array('name' => 'min_capacity','type' => 'xsd:int'),
                'granularity'  => array('name' => 'granularity','type' => 'xsd:int'),
        ));

        $server->wsdl->addComplexType('portTypeList','complexType','array','all','',
                array('ports' => array('name' => 'ports','type' => 'tns:portType')));

        $server->wsdl->addComplexType('deviceType','complexType','struct','all','',
                array('dev_id'  => array('name' => 'dev_id','type' => 'xsd:int'),
                'dev_descr' => array('name' => 'dev_descr','type' => 'xsd:string'),
                'ports' => array('name' => 'ports','type' => 'tns:portTypeList')));

        $server->wsdl->addComplexType('deviceTypeList','complexType','array','all','',
                array('devices' => array('name' => 'devices','type' => 'tns:deviceType')));

        $server->wsdl->addComplexType('netType','complexType','struct','all','',
                array('net_id'  => array('name' => 'net_id','type' => 'xsd:int'),
                'net_descr' => array('name' => 'net_descr','type' => 'xsd:string'),
                'devices' => array('name' => 'devices','type' => 'tns:deviceTypeList')));

        $server->wsdl->addComplexType('netTypeList','complexType','array','all','',
                array('nets' => array('name' => 'nets','type' => 'tns:netType')));

        $server->register(
                'getUsers',
                array('usr' => 'tns:userType'),
                array('usr_list'=> 'tns:userTypeList'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getUsers",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getGroups',
                array('grp' => 'tns:groupType'),
                array('grp_list'=> 'tns:groupTypeList'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getGroups",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getReqInfo',
                array('req_id'=>'xsd:int','dom_src_ip'=>'xsd:string'),
                array('req_info'=>'tns:reqType'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getReqInfo",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getFlowInfo',
                array('res_id'=>'xsd:int'),
                array('flow_info'=>'tns:flowType'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getFlowInfo",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getTimerInfo',
                array('res_id'=>'xsd:int'),
                array('timer_info'=>'tns:timerType'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getTimerInfo",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getURNInfo',
                array('urn_string'=>'xsd:string'),
                array('urn_info'=>'tns:urnType'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getURNsInfo",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getURNDetails',
                array('urn_string'=>'xsd:string'),
                array('urn_details'=>'tns:netTypeList'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getURNDetails",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'notifyResponse',
                array('name'=>'tns:responseType'),
                array('return'=>'xsd:string'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/notifyResponse",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'requestUserAuthorization',
                array('usr_dst' => 'xsd:int', 'request' => 'tns:requestType'),
                array('req_id' => 'xsd:int'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/requestUserAuthorization",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'requestGroupAuthorization',
                array('grp_dst'=>'xsd:int', 'request' => 'tns:requestType'),
                array('req_id'=>'xsd:int'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/requestGroupAuthorization",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'getRequestPath',
                array('req_id'=>'xsd:int', 'dom_src_ip' => 'xsd:string'),
                array('path_array' => 'tns:stringTypeList'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/getRequestPath",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        $server->register(
                'refreshRequestStatus',
                array('req_id'=>'xsd:int', 'dom_src_ip' => 'xsd:string', 'new_status' => 'xsd:string'),
                array('confirmation'=>'xsd:string'),
                $namespace,
                "http://$this_ip/$this_dir_name$this->app/ws/refreshRequestStatus",
                'rpc',
                'encoded',
                'Complex Hello World Method');

        function getUsers($usr) {
            Framework::debug('getusers',$usr);
            $user = new user_info();

            if (isset($usr) && is_array($usr)) {
                if (isset($usr['usr_id']) && is_int($usr['usr_id']))
                    $user->usr_id = $usr['usr_id'];
                if (isset($usr['usr_name']) && is_string($usr['usr_name']))
                    $user->usr_name = $usr['usr_name'];
            }

            $result = $user->fetch(FALSE);

            foreach ($result as $r)
                $return[] = array('usr_id' => $r->usr_id, 'usr_name' => $r->usr_name);

            if ($return)
                return $return;
            else return NULL;
        }

        function getGroups($grp) {
            Framework::debug('getgroups',$grp);
            $group = new group_info();

            if (isset($grp) && is_array($grp)) {
                if (isset($grp['grp_id']) && is_int($grp['grp_id']))
                    $group->grp_id = $grp['grp_id'];
                if (isset($grp['grp_descr']) && is_string($grp['grp_descr']))
                    $group->grp_descr = $grp['grp_descr'];
            }

            $result = $group->fetch(FALSE);

            foreach ($result as $r)
                $return[] = array('grp_id' => $r->grp_id, 'grp_descr' => $r->grp_descr);

            if ($return)
                return $return;
            else return NULL;
        }

        function getReqInfo($req_id, $dom_src_ip) {
            Framework::debug('getreqinfo',$req_id);

            $req = new request_info();
            $req->req_id = $req_id;

            $domain = new domain_info();
            $domain->oscars_ip = $dom_src_ip;
            $req->src_dom = $domain->get('dom_id');
            $req->answerable = 'no';

            if ($result = $req->fetch(FALSE)) {
                $rescTy = $result[0]->resource_type;

                $resource = new $rescTy();
                $pk = $resource->getPrimaryKey();
                $resource->{$pk} = $result[0]->resource_id;

                if ($result2 = $resource->fetch(FALSE)) {
                    $return = array('resc_id' => $result[0]->resource_id,
                            'resc_descr' => $result2[0]->res_name,
                            'resc_type' => $rescTy);

                    Framework::debug('return', $return);
                    return $return;
                }
            }
            return NULL;
        }

        function getFlowInfo($res_id) {
            Framework::debug('getflowinfo',$res_id);

            $reservation = new reservation_info();
            $reservation->res_id = $res_id;

            $flw_id = $reservation->get('flw_id');

            if (!$flw_id) {
                Framework::debug('reservation not found');
                return NULL;
            } else {

                $flow = new flow_info();
                $flow->flw_id = $flw_id;
                $dom_src_id = $flow->get('src_dom');
                $dom_dst_id = $flow->get('dst_dom');
                $domain = new domain_info();
                $domain->dom_id = $dom_src_id;
                $dom_src = $domain->get();

                $domain->dom_id = $dom_dst_id;
                $dom_dst = $domain->get();

                $return = array (
                        'src_dom_ip' => $dom_src->oscars_ip,
                        'dst_dom_ip' => $dom_dst->oscars_ip,
                        'bandwidth' => $reservation->get('bandwidth'),
                        'src_urn_string' =>  $flow->get('src_urn_string'),
                        'dst_urn_string' =>  $flow->get('dst_urn_string'),
                );
                Framework::debug('return do reqflowinfo', $return);
                return $return;
            }
        }


        /** necessita modificacao **/
        function getTimerInfo($res_id) {
            Framework::debug('gettimerinfo',$res_id);
            if (isset($res_id) && is_int($res_id)) {
                $reservation = new reservation_info();
                $reservation->res_id = $res_id;

                $res = $reservation->fetch(FALSE);

                if (!$res) {
                    Framework::debug('reservation not found');
                    return NULL;
                }

                $timer = new timer_info();
                $timer->tmr_id = $res[0]->tmr_id;
                $timer_info = $timer->fetch(FALSE);

                if (!$timer) {
                    Framework::debug('timer not found');
                    return NULL;
                }

                $return = array(
                        'start' => $timer_info[0]->start,
                        'finish' => $timer_info[0]->finish,
                        'recurrence' => $timer_info[0]->summary);

                return $return;
            } else return NULL;
        }

        /** necessita modificacao **/
        function getURNInfo($urn_string) {
            Framework::debug('geturninfo',$urn_string);

            if (isset($urn_string) && is_string($urn_string)) {
                $urn = new urn_info();
                $urn->urn_string = $urn_string;

                $result = $urn->fetch(FALSE);

                if (!$result) {
                    $urn_info = NULL;
                } else {

                    $net = new network_info();
                    $net->net_id = $result[0]->net_id;
                    $r_net = $net->fetch(FALSE);

                    $dev = new device_info();
                    $dev->dev_id = $result[0]->dev_id;
                    $r_dev = $dev->fetch(FALSE);

                    $urn_info = array ( 'net_descr' => $r_net[0]->net_descr,
                            'dev_descr' => $r_dev[0]->dev_descr,
                            'port_number' => $result[0]->port);
                }
            } else {
                $urn_info = NULL;
            }
            return $urn_info;
        }

        /** necessita modificacao **/
        function getURNDetails($urn_string) {
            Framework::debug('geturndetails',$urn_string);
            if (!isset($urn_string)) {
                return MeicanTopology::getURNDetails();
            } elseif (is_string($urn_string)) {
                return MeicanTopology::getURNDetails($urn_string);
            }
            return NULL;
        }

        function notifyResponse($response) {
            Framework::debug('acionando notify response', $response);
            $validResponses = array ("accept","reject");

            if (array_search($response['response'], $validResponses)) {

                $req = new request_info();
                $req->req_id = $response['req_id'];
                $domain = new domain_info();
                //o id do dom src aqui é o ip do OSCARS, supondo que é uma chave
                //única na tabela domain_info
                $domain->oscars_ip = $response['dom_src_ip'];

                $src_dom = $domain->get();
                $req->src_dom = $src_dom->dom_id;
                $req->answerable = 'no';

                if (!$req->get('response')) {

                    $req->updateTo(array('message' => $response['message'], 'response' => $response['response']), FALSE);

                    if ($response['response']=='accept') {

                        Framework::debug('setando campo send nos gris...');

                        $req->updateTo(array("status"=>"AUTHORIZED"),false);
                        $tmp = new gri_info();
                        $tmp->res_id = $req->get('resource_id');
                        $allgris = $tmp->fetch(FALSE);

                        foreach ($allgris as $g) {
                            $now = time();
                            $start = new DateTime($g->start);
                            if ($now < ($start->getTimestamp()-180)) //testa para ver se a reserva está NO MINIMO 3 minutos do tempo atual
                                $g->updateTo(array("send"=>1));
                            else  $g->updateTo(array("status"=>"TIMED OUT"));
                        }


                    } else { //requisicao negada
                        $req->updateTo(array("status"=>"DENIED"),false);
                        
                        //as reservas devem ser canceladas no OSCARS
                        $tmp = new gri_info();
                        $tmp->res_id = $req->get('resource_id');
                        $allgris = $tmp->fetch(FALSE);

                        foreach ($allgris as $g) {
                             $oscRes = new OSCARSReservation();
                             $oscRes->setOscarsUrl($src_dom->oscars_ip);
                             $oscRes->setGri($g->gri_descr);
                             if ($oscRes->cancelReservation()){
                                 //apaga os gris negados do db MEICAN
                                 $g->delete(FALSE);
                             } else {
                                 Framework::debug("error in cancel reservation gri ", $g->gri_descr);
                             }
                            unset($oscRes);
                        }
                    }

                    return true; //se a requisicao foi negada ou aceita retorna true

                } else {
                    Framework::debug("essa requisicao já havia sido respondida");
                    return null;
                }
            } else {
                Framework::debug("valor de response invalido");
                return null;
            }
        }

        function requestUserAuthorization($usr_dst, $request) {
            Framework::debug('requestuserauth', $request);
            //colocar embaixo do usuário destino
            if ($usr_dst && $request) {

                $new_request = new request_info();
                //$new_request->setDom('dom_src', $request['dom_src_ip']);

                $domain = new domain_info();

                //o id do dom src aqui é o ip do OSCARS, supondo que é uma chave
                //única na tabela domain_info
                $domain->oscars_ip = $request['dom_src_ip'];
                $src_dom = $domin->get();
                $new_request->src_dom = $src_dom->dom_id;

                //$new_request->setDom('dom_dst', $request['dom_dst_ip']);

                //o id do dom src aqui é o ip do OSCARS, supondo que é uma chave
                //única na tabela domain_info
                $domain->oscars_ip = $request['dom_dst_ip'];
                $dst_dom = $domin->get();
                $new_request->src_dom = $dst_dom->dom_id;

                $new_request->answerable = 'yes';
                $new_request->req_id = $request['req_id'];
                $new_request->src_usr = $request['usr_src'];

                //insere embaixo do usuario passado como parametro
                $user = new user_info();
                $user->usr_id = $usr_dst;
                $resuser = $user->fetch(FALSE);

                if ($resuser) {
                    if ($new_request->insert($usr_dst,'user_info')) {

                        return TRUE;

                    } else {
                        Framework::debug('fail to save the request by requestUserAuthorization');
                        return NULL;
                    }

                } else {
                    Framework::debug('user dst not found');
                    return NULL;
                }
            } else {
                Framework::debug('requestUserAuthorization without request set or user');
                return NULL;
            }
        }

        function requestGroupAuthorization($grp_dst, $request) {
            Framework::debug('requestgroupauth', $grp_dst);
            Framework::debug('requestgroupauth', $request);
            //colocar embaixo do grupo destino

            if ($grp_dst && $request) {

                $new_request = new request_info();

                $domain = new domain_info();

                //o id do dom src aqui é o ip do OSCARS, supondo que é uma chave
                //única na tabela domain_info
                $domain->oscars_ip = $request['dom_src_ip'];
                $src_dom = $domin->get();
                $new_request->src_dom = $src_dom->dom_id;

                //$new_request->setDom('dom_dst', $request['dom_dst_ip']);

                //o id do dom src aqui é o ip do OSCARS, supondo que é uma chave
                //única na tabela domain_info
                $domain->oscars_ip = $request['dom_dst_ip'];
                $dst_dom = $domin->get();
                $new_request->src_dom = $dst_dom->dom_id;

                $new_request->req_id = $request['req_id'];
                $new_request->src_usr = $request['usr_src'];
                $new_request->answerable = 'yes';

                //insere embaixo do grupo passado como parametro
                $group = new group_info();
                $group->grp_id = $grp_dst;
                $resgroup = $group->fetch(FALSE);

                if ($resgroup) {
                    if ($new_request->insert($grp_dst,'group_info'))
                        return TRUE;
                    else {
                        Framework::debug('fail to save the request by requestUserAuthorization');
                        return NULL;
                    }

                } else {
                    Framework::debug('group dst not found');
                    return NULL;
                }

            } else {
                Framework::debug('requestUserAuthorization without request set');
                return NULL;
            }
        }

        function getRequestPath($req_id, $dom_src_ip) {

            $path = array (
                    0 => Configure::read('domIp'),
                    1 => 'noc.inf.ufrgs.br:65502',
                    2 => 'noc.inf.ufrgs.br:65503');

            return $path;

        }

        function refreshRequestStatus($req_id, $dom_src_ip, $new_status) {
            Framework::debug('refreshreqstatus', $new_status);
            $req = new request_info();
            $req->req_id = $req_id;
            $req->setDom('dom_src', $dom_src_ip);

            if ($result=$req->fetch(FALSE)) {
                if ($new_status)
                    if ($update=$req->updateTo(array('status'=>$new_status), FALSE))
                        return TRUE;
            }
            return NULL;
        }
        $POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
        $server->service($POST_DATA);
    }
}
?>
