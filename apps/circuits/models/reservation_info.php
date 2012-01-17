<?php

include_once 'libs/resource_model.php';

include_once 'apps/circuits/models/gri_info.php';
include_once 'apps/circuits/models/flow_info.php';
include_once 'apps/circuits/models/timer_info.php';
include_once 'apps/circuits/models/oscars_reservation.php';

include_once 'apps/bpm/models/request_info.php';

class reservation_info extends Resource_Model {
    var $displayField = "res_name";

    public function reservation_info() {
        $this->setTableName("reservation_info");

        // Add all table attributes
        $this->addAttribute("res_id", "INTEGER", true, false, false);
        $this->addAttribute("res_name", "VARCHAR");

        $this->addAttribute("bandwidth", "INTEGER");
        $this->addAttribute("flw_id", "INTEGER");
        $this->addAttribute("tmr_id", "INTEGER");

        $this->addAttribute("creation_time", "VARCHAR");
    }
    
    public function getStatus() {

        if (!$this->res_id)
            return FALSE;

        $gri = new gri_info();
        $gri->res_id = $this->res_id;
        $gris = $gri->fetch(FALSE);

        $req = new request_info();
        $req->resource_id = $this->res_id;
        $req->resource_type = 'reservation_info';
        $request = $req->fetch();

        $status = "UNKNOWN";
        $now = time();

        if ($gris) {
            $next_gri = $gris[0];

            foreach ($gris as $g) {
                // executa lógica para determinar qual status mostrar na página
                $date = new DateTime($g->start);
                $start = $date->getTimestamp();

                $date = new DateTime($next_gri->start);
                $next_start = $date->getTimestamp();

                $new_diff = $start - $now;
                $next_diff = $next_start - $now;

                if (($new_diff > 0) && (($new_diff < $next_diff) || ($next_diff < 0))) {
                    // é o GRI cujo status será mostrado na página
                    $next_gri = $g;
                }
            }

            if ($request) {
                if ($request[0]->response == 'reject')
                    $status = 'REJECTED';
                elseif ($request[0]->response == 'accept')
                    $status = $next_gri->status;
                else
                    $status = ($request[0]->status) ? $request[0]->status : "UNKNOWN";
            } else
                $status = $next_gri->status;
        } else {
            $status = "NO_GRI";
        }

        return $status;
    }

    public function getReservationDetails() {
        $res = $this->fetch(FALSE);

        if (!$res) {
            Framework::debug('reservation not found');
            return FALSE;
        }

        $flow = new flow_info();
        $flow->flw_id = $res[0]->flw_id;
        $flow = $flow->fetch(FALSE);

        $flow_info = $flow[0]->getFlowDetails();

        $timer = new timer_info();
        $timer->tmr_id = $res[0]->tmr_id;
        $tmp = $timer->fetch();
        $timer_info = $tmp[0];

        $return = array(
                'res_id' => $this->res_id,
                'res_name' => $res[0]->res_name,
                'dom_src' => $flow_info->source->oscars_ip,
                'urn_src' => $flow_info->source->urn,
                'dom_dst' => $flow_info->dest->oscars_ip,
                'urn_dst' => $flow_info->dest->urn,
                'bandwidth' => $res[0]->bandwidth,
                'path' => $flow_info->path,
                'timer_begin' => $timer_info->begin_timestamp,
                'timer_end' => $timer_info->end_timestamp,
                'timer_rec' => $timer_info->summary
        );

        $object = (object) $return;

        return $object;
    }

    public function sendForAuthorization() {

        //cria nova request
        $newReq = new request_info();
        $newReq->setDom('dom_src', Framework::$domIp);
        $newReq->req_id = $newReq->getNextId('req_id');


        //para buscar o dom_dst_ip
        $flow = new flow_info();
        $flow->flw_id = $this->flw_id;
        $res_flow = $flow->getFlowDetails2();
        $newReq->dom_dst = $res_flow->dst_dom_id;

        $newReq->usr_src = AuthSystem::getUserId();
        //acho q nao vai precisar possuir res_id na tabela de requests
        //$newReq->res_id = $this->res_id;
        $newReq->resource_type = 'reservation_info';
        $newReq->resource_id = $this->res_id;
        $newReq->answerable = 'no';

        Framework::debug('dentro do sendauth');
        $endpoint = Framework::$odeWSDLToRequest;
        //$endpoint = "http://".Framework::$odeIp."/ode/deployment/bundles/v1_workflow_pietro/processes/v1_workflow_pietro/processes.ode/diagrama-v1-workflow_pietro.wsdl";

        if ($client = new SoapClient($endpoint, array('cache_wsdl' => 0))) {

            $requestSOAP = array(
                    'req_id' => $newReq->req_id,
                    'dom_src_ip' => $res_flow->src_dom_ip,
                    'dom_dst_ip' => $res_flow->dst_dom_ip,
                    'usr_src' => $newReq->usr_src);

            Framework::debug('ira enviar para autorizaçao...', $requestSOAP);
            if ($result = $client->Start_v1_workflow_felipe($requestSOAP)) {
                $newReq->status = 'SENT FOR AUTHORIZATION';
                //insere a requisicao no banco local
                if ($req = $newReq->insert())
                //atualiza tabela da reserva com o id da requisicao
                    return TRUE;

                else {
                    Framework::debug('failed to insert request at local database');
                    return FALSE;
                }

            } else {
                Framework::debug('cant call soap operation at ',$endpoint);
                return FALSE;
            }
        } else {
            Framework::debug('cant call soap client at ',$endpoint);
            return FALSE;
        }
    }

    function getGriDetails() {
        $gri_to_list = array('args0' => array('ufrgs.cipo.rnp.br-1259', 'ufrgs.cipo.rnp.br-1694'));

        $now = mktime()+5*60;
        $now_10 = mktime()+10*60;

        $array_to_create = array('args0' => array(
                        'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFRGS-CIPO-RNP-001:port=5:link=*', // source
                        'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFRGS-CIPO-RNP-002:port=5:link=*', // dest
                        '100', // banda
                        $now, // begin TS
                        $now_10, // end TS

                        'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFRGS-CIPO-RNP-002:port=5:link=*', // source
                        'urn:ogf:network:domain=ufsc.cipo.rnp.br:node=UFRGS-CIPO-RNP-001:port=5:link=*', // dest
                        '100', // banda
                        $now, // begin TS
                        $now_10 // end TS
        ));

        $endpoint = "http://".Framework::$bridgeIp."/axis2/services/BridgeOSCARS?wsdl";
        if ($client = new SoapClient($endpoint, array('cache_wsdl' => 0))) {
            if ($result = $client->list($gri_to_list))
                Framework::debug('result list oscars', $result);

            if ($result = $client->create($array_to_create))
                Framework::debug('result create oscars', $result);
        }
    }

    function getFlow(){
        $tmp = $this->fetch();
        return $tmp[0]->flw_id;
    }
}

?>