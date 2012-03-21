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

        /*$server->wsdl->addComplexType('stringTypeList','complexType','array','all','',
                array('str' => array('name' => 'str','type' => 'xsd:string')));
         * 
         */
        
        $server->wsdl->addComplexType('stringTypeList','complexType','array','','SOAP-ENC:Array',array(),
                array( array('ref' => 'SOAP-ENC:arrayType','wsdl:arrayType' => 'xsd:string[]'),
                'xsd:string'));

        $server->wsdl->addComplexType('reqType','complexType','struct','all','',
                array('resc_id' => array('name' => 'resc_id','type' => 'xsd:int'),
                'resc_descr' => array('name' => 'resc_descr','type' => 'xsd:string'),
                'resc_type' => array('name' => 'resc_type','type' => 'xsd:string')));

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
                array('req_id'=>'xsd:int'),
                array('ode_ip_array' => 'tns:stringTypeList'),
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

        function getReqInfo($req_id, $dom_src_ip) {
            debug('getreqinfo',$req_id);

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

                    debug('return', $return);
                    return $return;
                }
            }
            return NULL;
        }

        function notifyResponse($response) {
            debug('acionando notify response', $response);
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

                        debug('setando campo send nos gris...');

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
                                 debug("error in cancel reservation gri ", $g->gri_descr);
                             }
                            unset($oscRes);
                        }
                    }

                    return true; //se a requisicao foi negada ou aceita retorna true

                } else {
                    debug("essa requisicao já havia sido respondida");
                    return null;
                }
            } else {
                debug("valor de response invalido");
                return null;
            }
        }

        function requestUserAuthorization($usr_dst, $request) {
            debug('requestuserauth', $request);
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
                        debug('fail to save the request by requestUserAuthorization');
                        return NULL;
                    }

                } else {
                    debug('user dst not found');
                    return NULL;
                }
            } else {
                debug('requestUserAuthorization without request set or user');
                return NULL;
            }
        }

        function requestGroupAuthorization($grp_dst, $request) {
            debug('requestgroupauth', $grp_dst);
            debug('requestgroupauth', $request);
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
                        debug('fail to save the request by requestUserAuthorization');
                        return NULL;
                    }

                } else {
                    debug('group dst not found');
                    return NULL;
                }

            } else {
                debug('requestUserAuthorization without request set');
                return NULL;
            }
        }

        function getRequestPath($req_id) {
            $gri = "gri";
            $oscars_url = "http";
            
            $reservation = new reservation_info();
            $ode_ip_array = $reservation->getPath($oscars_url, $gri);

            return $ode_ip_array;
        }

        function refreshRequestStatus($req_id, $dom_src_ip, $new_status) {
            debug('refreshreqstatus', $new_status);
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
