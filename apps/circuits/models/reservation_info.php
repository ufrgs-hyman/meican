<?php

include_once 'libs/Model/resource_model.php';

include_once 'apps/circuits/models/gri_info.php';
include_once 'apps/circuits/models/flow_info.php';
include_once 'apps/circuits/models/timer_info.php';
include_once 'apps/circuits/models/oscars_reservation.php';
include_once 'apps/circuits/models/OSCARSVersionTester.php';

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
        $this->addAttribute("usr_id", "INTEGER");

        $this->addAttribute("creation_time", "VARCHAR");
    }
    
    public function getStatus() {

        if (!$this->res_id)
            return FALSE;

        $gri = new gri_info();
        $gri->res_id = $this->res_id;
        $gris = $gri->fetch(FALSE);
        
        //Log::write('debug', "get status:\n" . print_r($gris,true));

        $req = new request_info();
        $req->resource_id = $this->res_id;
        $req->resource_type = 'reservation_info';
        $req->answerable = 'no';
        $request = $req->fetch(false);

        $status_obj = new stdClass();

        if ($gris) {
            if ($request && $request[0]->response != 'accept') {
                // show request status
                if ($request[0]->response == 'reject') {
                    // reservation request was denied
                    $status_obj->status = gri_info::translateStatus('REJECTED');
                    $status_obj->original_status = 'REJECTED';
                } else {
                    // reservation request is pending
                    $status = ($request[0]->status) ? $request[0]->status : "UNKNOWN";
                    $status_obj->status = gri_info::translateStatus($status);
                    $status_obj->original_status = "REQ_PENDING";
                }
            } else {
                // show GRI status

                $now = time();
                $gri_to_show = $gris[0];

                foreach ($gris as $g) {
                    // executa lógica para determinar qual status mostrar na página
                    $date_tmp = new DateTime($g->start);
                    $start = $date_tmp->getTimestamp();

                    $date_tmp = new DateTime($g->finish);
                    $finish = $date_tmp->getTimestamp();

                    $date_tmp = new DateTime($gri_to_show->start);
                    $to_show_start = $date_tmp->getTimestamp();

                    $date_tmp = new DateTime($gri_to_show->finish);
                    $to_show_finish = $date_tmp->getTimestamp();

                    // se tempo atual for antes do GRI, calcula a diferença, senão deixa FALSE
                    $start_diff = ($now < $start) ? $start - $now : FALSE;
                    $start_show_diff = ($now < $to_show_start) ? $to_show_start - $now : FALSE;

                    // se tempo atual for depois do GRI, calcula a diferença, senão deixa FALSE
                    $finish_diff = ($now > $finish) ? $now - $finish : FALSE;
                    $finish_show_diff = ($now > $to_show_finish) ? $now - $to_show_finish : FALSE;

                    if ($now >= $start && $now <= $finish) {
                        // período do GRI é o tempo atual: encerra iteração, pois sempre será um só
                        $gri_to_show = $g;
                        break;
                    } elseif (($start_diff !== FALSE) && ($start_show_diff !== FALSE) && ($start_diff < $start_show_diff)) {
                        // tempo está antes e GRI é o próximo
                        $gri_to_show = $g;
                    } elseif (($finish_diff !== FALSE) && ($finish_show_diff !== FALSE) && ($finish_diff < $finish_show_diff)) {
                        // tempo já passou e GRI foi o mais recente
                        $gri_to_show = $g;
                    } elseif (($start_diff !== FALSE) && ($finish_show_diff !== FALSE)) {
                        // tempo está entre o tempo dos GRIs, mostra o próximo
                        $gri_to_show = $g;
                    }
                }
                
                $status_obj->status = gri_info::translateStatus($gri_to_show->status);
                $status_obj->original_status = $gri_to_show->status;
            }
        } else {
            $status = "NO_GRI";
            $status_obj->status = gri_info::translateStatus($status);
            $status_obj->original_status = $status;
        }

        return $status_obj;
    }
    
    /**
     *
     * @param Array $resIdArray An array containing reservation IDs to filter by status
     * @return Array An array containing all reservation objects that user has permission to view
     */
    public function getReservationsToShow($resIdArray=array()) {
        /**
         * reservations that user has permission to read, e.g., enginner can read reservations from his domain
         */
        $res_info = new reservation_info();
        if ($resIdArray)
            $res_info->res_id = $resIdArray;
        $topologyReservations = $res_info->fetch();
        
        /**
         * initialize the array containing the reservations to show
         */
        $resevartionsToShow = $topologyReservations;
        
        /**
         * reservations that the user has requested
         */
        $ures_info = new reservation_info();
        $ures_info->usr_id = AuthSystem::getUserId();
        if ($userReservations = $ures_info->fetch(FALSE)) {
            if (empty($topologyReservations)) {
                // user has only self-requested reservations
                $resevartionsToShow = $userReservations;
            } else {
                // user has both type of reservations, merge them
                $topResIdArray = Common::arrayExtractAttr($topologyReservations, "res_id");
                foreach ($userReservations as $res) {
                    if (array_search($res->res_id, $topResIdArray) === FALSE) {
                        array_push($resevartionsToShow, $res);
                    }
                }
            }
        }
        
        return $resevartionsToShow;
    }

    public function getReservationDetails() {
        $res = $this->fetch(FALSE);

        if (!$res) {
            debug('reservation not found');
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
        
        $user = new user_info();
        $user->usr_id = $res[0]->usr_id;
        $tmp = $user->fetch();
        $user_info = $tmp[0];

        $return = array(
                'res_id' => $this->res_id,
                'res_name' => $res[0]->res_name,
                'dom_src' => $flow_info->source->idc_url,
                'urn_src' => $flow_info->source->urn,
                'dom_dst' => $flow_info->dest->idc_url,
                'urn_dst' => $flow_info->dest->urn,
                'bandwidth' => $res[0]->bandwidth,
                'path' => $flow_info->path,
                'timer_begin' => $timer_info->begin_timestamp,
                'timer_end' => $timer_info->end_timestamp,
                'timer_rec' => $timer_info->summary,
                'usr_login' => $user_info->usr_login
        );

        $object = (object) $return;

        return $object;
    }

    public function sendForAuthorization() {

        //cria nova request
        $newReq = new request_info();
        $newReq->setDom('dom_src', Configure::read('domIp'));
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

        debug('dentro do sendauth');
        $endpoint = Configure::read('odeWSDLToRequest');
        //$endpoint = "http://".odeip."/ode/deployment/bundles/v1_workflow_pietro/processes/v1_workflow_pietro/processes.ode/diagrama-v1-workflow_pietro.wsdl";

        if ($client = new SoapClient($endpoint, array('cache_wsdl' => 0))) {

            $requestSOAP = array(
                    'req_id' => $newReq->req_id,
                    'dom_src_ip' => $res_flow->src_dom_ip,
                    'dom_dst_ip' => $res_flow->dst_dom_ip,
                    'usr_src' => $newReq->usr_src);

            debug('ira enviar para autorizaçao...', $requestSOAP);
            if ($result = $client->Start_v1_workflow_felipe($requestSOAP)) {
                $newReq->status = 'SENT FOR AUTHORIZATION';
                //insere a requisicao no banco local
                if ($req = $newReq->insert())
                //atualiza tabela da reserva com o id da requisicao
                    return TRUE;

                else {
                    debug('failed to insert request at local database');
                    return FALSE;
                }

            } else {
                debug('cant call soap operation at ',$endpoint);
                return FALSE;
            }
        } else {
            debug('cant call soap client at ',$endpoint);
            return FALSE;
        }
    }
    
    public function getPath() {
        $flow_info = new flow_info();
        
        if ($this->flw_id)
            $flow_info->flw_id = $this->flw_id;
        else {
            if (!$this->res_id)
                return FALSE;
            
            $flow_info->flw_id = $this->get('flw_id',false);
        }
            
        $path = null;
        if (isset($flow_info->flw_id))
            $path = $flow_info->get('path', false);
        
        $pathArray = array();

        if ($path) {
            //Log::write("circuits", "Path found in local database...");
            $pathArray = explode(';', $path);
        } else {
            if (!$this->res_id) {
                if ($this->flw_id) {
                    $res_tmp = new reservation_info();
                    $res_tmp->flw_id = $this->flw_id;
                    $this->res_id = $res_tmp->get('res_id',false);
                } else
                    return FALSE;
            }
            
            Log::write("circuits", "Path not found, trying OSCARS...");
            
            $gri_info = new gri_info();
            $gri_info->res_id = $this->res_id;
            $gri = $gri_info->fetch(FALSE);

            $domain_info = new domain_info();
            $domain_info->dom_id = $gri[0]->dom_id;
            $domain = $domain_info->fetch(FALSE);
            
            //$oscars = new OSCARSReservation();				// OLD DESIGN            
            $versTest = new OSCARSVersionTester($domain_info->getDomVersion());	// Added by Jeremy
            $oscars = $versTest->checkVersion();				// NEW DESIGN -- Added by Jeremy

            $oscars->setGri($gri[0]->gri_descr);
            $oscars->setOscarsUrl($domain[0]->idc_url);

            $response = FALSE;
            $cont = 0;

            Log::write("circuits", "Getting GRI path:\n" . print_r(array("OSCARS URL" => $domain[0]->idc_url, "GRI" => $gri[0]->gri_descr), TRUE));


            while (!$response && $cont < 15) {
                if ($oscars->queryReservation()) {
                    $status = $oscars->getStatus();
                    if (($status == "PENDING") || ($status == "ACTIVE") || ($status == "FINISHED") || ($status == "FAILED") || ($status == "CANCELLED")) {
                        if ($pathArray = explode(";", $oscars->getPath())) {
                            $pathArray = array_filter($pathArray, 'strlen');
                            Log::write("circuits", "Get path sucessful. Complete path:\n" . print_r($pathArray, TRUE));
                            $response = TRUE;
                        }
                    } elseif (($status == "INCREATE") || ($status == "ACCEPTED")) {
                        sleep(2);
                        $cont++;
                        continue;
                        //} elseif (($status == "FAILED") || ($status == "CANCELLED")) {
//                } elseif ($status == "CANCELLED") {
//                    Log::write("error", "Couldn't get path. Reservation FAILED or CANCELLED");
//                    $pathArray = FALSE;
//                    $response = TRUE;
//                }
                    }
                } else {
                    Log::write("error", "Couldn't get path. Query reservation failed");
                    $pathArray = null;
                    $response = true;
                }
            }
            $pathString = implode(';', $pathArray);
            $flow_info->updateTo(array('path' => $pathString), false);
        }
        return $pathArray;
    }
    
    static public function getAvailableBandwidth($res_id, $gri_id = null) {
        $available_bands = array();
        
        if ($res_id) {
            $gri_info = new gri_info();
            $gri_info->res_id = $res_id;

            if ($gri_id)
                $gri_info->gri_id = $gri_id;

            if ($gris = $gri_info->fetch(false)) {
                foreach ($gris as $gri) {
                    $capacity = 1000;
                    $linkUtilization = 0;

                    $conflicted = gri_info::getConflictedGris($gri->start, $gri->finish, $res_id);

                    foreach ($conflicted as $cg) {
                        $linkUtilization += $cg->bandwidth;
                    }

                    $available_bands[] = $capacity - $linkUtilization;

//                    Log::write("debug", "Start: " . print_r($gri->start, true));
//                    Log::write("debug", "Finish: " . print_r($gri->finish, true));
//                    Log::write("debug", "Available Band: " . print_r($capacity, true) . " - " . print_r($linkUtilization, true));
                }
            }
        }
        
        return $available_bands;
    }

}

?>
