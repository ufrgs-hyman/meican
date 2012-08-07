<?php

defined('__MEICAN') or die("Invalid access.");

include_once 'libs/controller.php';

include_once 'libs/common.php';

include_once 'apps/circuits/controllers/flows.php';
include_once 'apps/circuits/controllers/timers.php';

include_once 'apps/circuits/models/reservation_info.php';
include_once 'apps/circuits/models/gri_info.php';
include_once 'apps/circuits/models/flow_info.php';
include_once 'apps/circuits/models/timer_info.php';
include_once 'apps/circuits/models/client_info.php';
include_once 'apps/circuits/models/oscars_reservation.php';

include_once 'apps/aaa/models/user_info.php';

include_once 'apps/bpm/models/request_info.php';

include_once 'apps/topology/models/domain_info.php';
include_once 'apps/topology/models/topology.php';

include_once 'libs/Vendors/nuSOAP/lib/nusoap.php';

class reservations extends Controller {

    public function show($filterArray=array()) {
        // inicializa variável de sessão
        Common::destroySessionVariable('res_begin_timestamp');

        $res_info = new reservation_info();
        if ($filterArray)
            $res_info->res_id = $filterArray;
        
        $allResevartionsToShow = $res_info->fetch();
        //$allResevartionsToShow = $res_info->getReservationsToShow($filterArray);
            
        if ($allResevartionsToShow) {
            $reservations = array();
            $src_domains = array();

            foreach ($allResevartionsToShow as $r) {
                $res = new stdClass();
                $res->id = $r->res_id;
                $res->name = $r->res_name;
                $res->bandwidth = $r->bandwidth;
                
                $status_obj = $r->getStatus();
                $res->original_status = $status_obj->original_status;
                $res->status = $status_obj->status;

                $flow = new flow_info();
                $flow->flw_id = $r->flw_id;
                $res->flow = $flow->getFlowDetails(false);

                $timer = new timer_info();
                $timer->tmr_id = $r->tmr_id;
                $res->timer = $timer->getTimerDetails();
                
                /*
                $user = new user_info();
                $user->usr_id = $r->usr_id;
                $res->usr_login = $user->get('usr_login', FALSE);
                 */

                $dom = new domain_info();
                if ($domain = $dom->getOSCARSDomain($res->flow->source->urn)) {
                    $res->flow->source->domain = $domain->dom_descr;
                    $res->flow->source->dom_id = $domain->dom_id;
                    $src_domains[] = $domain->dom_id;
                } else {
                    $urn = new urn_info();
                    $urn->urn_string = $res->flow->source->urn;
                    $urn_id = $urn->get('urn_id',FALSE);

                    $urn_aco = new Acos($urn_id, "urn_info");
                    $dev_aco = $urn_aco->getParentNodes();
                    $net_aco = $dev_aco[0]->getParentNodes();
                    $dom_aco = $net_aco[0]->getParentNodes();

                    $dom = new domain_info();
                    $dom->dom_id = $dom_aco[0]->obj_id;
                    $res->flow->source->domain = $dom->get('dom_descr');
                    $res->flow->source->dom_id = $dom->get('dom_id');
                    $src_domains[] = $dom_aco[0]->obj_id;
                }

                $dom = new domain_info();
                if ($domain = $dom->getOSCARSDomain($res->flow->dest->urn)) {
                    $res->flow->dest->domain = $domain->dom_descr;
                } else {
                    $urn = new urn_info();
                    $urn->urn_string = $res->flow->dest->urn;
                    $urn_id = $urn->get('urn_id',FALSE);

                    $urn_aco = new Acos($urn_id, "urn_info");
                    $dev_aco = $urn_aco->getParentNodes();
                    $net_aco = $dev_aco[0]->getParentNodes();
                    $dom_aco = $net_aco[0]->getParentNodes();

                    $dom = new domain_info();
                    $dom->dom_id = $dom_aco[0]->obj_id;
                    $res->flow->dest->domain = $dom->get('dom_descr');
                }
                
                $reservations[] = $res;
            }

            if ($this->action == "status") {
                $domains_to_js = array_unique($src_domains);

                $this->setArgsToScript(array(
                    'domains' => $domains_to_js,
                    'str_error_refresh_status' => _("Error to get status")
                ));
                $this->addScriptForLayout(array('reservations', 'reservations_status'));
            }
            
            $this->set(array(
                'reservations' => $reservations,
                'refresh' => ($this->action == 'status') ? 1 : 0
            ));
            $this->render('show');
        } else {
            $args = new stdClass();
            $args->title = ($this->action == 'status') ? _("Active and pending reservations") : _("History reservations");
            $args->message = ($this->action == 'status') ? _("You have no active or pending reservation, try <a href='history'>history</a> or click the button below to create a <a href='add'>new</a> one")
                    : _("You have no reservation in history, click the button below to create a <a href='add'>new reservation</a>");
            $args->link = array("action" => "add");
            $this->setArgsToBody($args);
            
            $this->render('empty');
        }
    }
    
    public function status() {
        $gri = new gri_info();
        $resIdArray = $gri->getStatusResId();
        $this->action = "status";
        $this->show($resIdArray);
    }
    
    public function history() {
        $gri = new gri_info();
        $resIdArray = $gri->getHistoryResId();
        $this->action = "history";
        $this->show($resIdArray);
    }

    public function refresh_status() {

        $dom_id = Common::POST('dom_id');
        
        $gris = new gri_info();
        $resToRefresh = $gris->getStatusResId($dom_id);
        
        //debug("res array to refresh",$resToRefresh);
        
        $res_info = new reservation_info();
        $res_info->res_id = $resToRefresh;
        $reservations = $res_info->fetch();
        //$reservations = $res_info->getReservationsToShow($resToRefresh);
        
        //debug("res to refresh",$reservations);

        /**
         * Transforma a lista bidimensional de gris para uma lista unidimensional -> para realizar uma só consulta ao OSCARS
         * Preenche vetor griList
         */
        $griList = array();
        if ($reservations) {
            foreach ($reservations as $res) {
                $gri = new gri_info();
                $gri->res_id = $res->res_id;
                $gri->dom_id = $dom_id;
                $gris = $gri->fetch(FALSE);

                if ($gris) {
                    foreach ($gris as $g) {
                        $griList[] = $g->gri_descr;
                    }
                }
            }
        } else {
            CakeLog::write('debug', "Falha ao buscar reservas no refresh status");
            return $this->renderJson(FALSE);
        }
        
        /**
         * Realiza a consulta com o vetor preenchido
         */
        $statusResult = array();
        if ($griList) {
            $dom = new domain_info();
            $dom->dom_id = $dom_id;
            $idc_url = $dom->get('idc_url',false);
            
            CakeLog::write('debug', "gri list ro refresh:\n" . print_r($griList,true));

            $oscarsRes = new OSCARSReservation();
            $oscarsRes->setOscarsUrl($idc_url);
            $oscarsRes->setGrisString($griList);

            if ($oscarsRes->listReservations()) {
                $statusResult = $oscarsRes->getStatusArray();
            } else {
                CakeLog::write('debug', "Falha ao conectar OSCARS ($idc_url) no refresh status");
                return $this->renderJson(FALSE);
            }
        }

        if (count($statusResult) != count($griList)) {
            CakeLog::write('debug', "Problema de consistencia na refresh status " . print_r($statusResult, true));
            return $this->renderJson(FALSE);
        }

        //CakeLog::write('debug', "refresh status result:\n" . print_r($statusResult,true));

        /**
         * Atualiza no banco os status que mudaram
         * Recalcula novo status para cada reserva
         */
        $cont = 0;
        $statusList = array();
        foreach ($reservations as $res) {
            $gri = new gri_info();
            $gri->res_id = $res->res_id;
            $gris = $gri->fetch(FALSE);
            
            if ($gris) {
                foreach ($gris as $g) {
                    // testa se status atual da GRI é diferente do status que retornou do OSCARS
                    if ($statusResult[$cont] != $g->status) {
                        // atualiza o banco de dados com o novo status (retornado do OSCARS)
                        $gri_tmp = new gri_info();
                        $gri_tmp->gri_id = $g->gri_id;
                        $gri_tmp->updateTo(array('status' => $statusResult[$cont]), FALSE);
                    }
                    $cont++;
                }
            }
            
            /** 
             * recalcula novo status
             * @todo algum tipo de cache para não executar getStatus toda vez
             */
            $status = $res->getStatus();
            
            $status_obj = new stdClass();
            $status_obj->id = $res->res_id;
            $status_obj->original_status = $status->original_status;
            $status_obj->status = $status->status;
            
            $statusList[] = $status_obj;
        }

        $this->renderJson($statusList);
    }

    public function gri_refresh_status() {

        $res_id = Common::POST("res_id");

        $gri = new gri_info();
        $gri->res_id = $res_id;
        $gris = $gri->fetch(FALSE);
        if ($gris) {
            $statusList = array();

            // testa se tem requisição, se tem, então mostra o status do ODE
            $req = new request_info();
            $req->resource_id = $res_id;
            $req->resource_type = 'reservation_info';
            $req->answerable = 'no';
            
            //CakeLog::write("debug","res req".print_r($req,true));
            
            $request = $req->fetch(false);
            //CakeLog::write("debug","request".print_r($request,true));

            if ($request && $request[0]->response != 'accept') {
                // show request status
                // a reserva possui requisição
                foreach ($gris as $g) {
                    $status_obj = new stdClass();
                    $status_obj->id = $g->gri_id;

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

                    $statusList[] = $status_obj;
                }
            } else {
                // show GRI status
                // consulta o OSCARS

                $control = array();
                $griList = array();

                $ind = 0;
                foreach ($gris as $g) {
                    switch ($g->status) {
                        case "FINISHED":
                        case "CANCELLED":
                        case "FAILED":
                            $control[$ind] = FALSE;
                            break;
                        default:
                            $griList[] = $g->gri_descr;
                            $control[$ind] = TRUE;
                    }
                    $ind++;
                }

                if ($griList) {
                    $dom = new domain_info();
                    $dom->dom_id = $gris[0]->dom_id;
                    $idc_url = $dom->get('idc_url');

                    $oscarsRes = new OSCARSReservation();
                    $oscarsRes->setOscarsUrl($idc_url);
                    $oscarsRes->setGrisString($griList);

                    if ($oscarsRes->listReservations()) {
                        $statusResult = $oscarsRes->getStatusArray();
                    } else {
                        CakeLog::write("error", "Fail to connect to OSCARS in refresh status");
                        return $this->renderJson(FALSE);
                    }

                    $ind = 0;
                    $cont = 0;

                    foreach ($gris as $g) {
                        if ($control[$ind]) {
                            // se posição no control for TRUE, é porque atualizou o status
                            $newStatus = $statusResult[$cont];
                            $cont++;

                            // testa se status atual da GRI é diferente do status que retornou do OSCARS
                            if ($g->status != $newStatus) {
                                $g->status = $newStatus;

                                // atualiza o banco de dados com o novo status (retornado do OSCARS)
                                $gri_tmp = new gri_info();
                                $gri_tmp->gri_id = $g->gri_id;
                                $gri_tmp->updateTo(array('status' => $newStatus), FALSE);
                            }
                        }

                        $status_obj = new stdClass();
                        $status_obj->id = $g->gri_id;
                        $status_obj->original_status = $g->status;
                        $status_obj->status = gri_info::translateStatus($g->status);
                        $statusList[] = $status_obj;

                        $ind++;
                    }
                }
            }

            $this->renderJson($statusList);
        } else {
            CakeLog::write("error", "Fail to get GRIs in refresh status");
            $this->renderJson(FALSE);
        }
    }
    
    public function add_form() {
        $this->add();
    }

    public function add() {
        // get Timestamp to calc reservation creation time by user
        Common::setSessionVariable("res_begin_timestamp", microtime(true));

        // STEP 1 VARIABLES ---------------------
        //$name = "Default_reservation_name";
        //---------------------------------------
        // STEP 2 VARIABLES ---------------------
        $domain = new domain_info();
        $allDomains = $domain->fetch(FALSE);
        //$allUrns = array();
        $domToMapArray = array();
        //$domains = array();

        foreach ($allDomains as $d) {
//            $dom = new stdClass();
//            $dom->id = $d->dom_id;
//            $dom->name = $d->dom_descr;
//            $dom->topology_id = $d->topo_domain_id;
//            $domains[] = $dom;

            if ($networks = MeicanTopology::getURNDetails($d->dom_id)) {
                $domain = new stdClass();
                $domain->id = $d->dom_id;
                $domain->name = $d->dom_descr;
                $domain->topology_id = $d->topology_id;
                $before = microtime(true);

                $domain->networks = $networks;
                $domToMapArray[] = $domain;
                //debug("tempo", (microtime(true) - $before));

                //$urns_tmp = Common::arrayExtractAttr(MeicanTopology::getURNs($d->dom_id), 'urn_string');
                //array_push($allUrns, $urns_tmp);
            }
        }
        
        // array for autoComplete host
        $client = new client_info();
        $hostArray = array();
        $hostArray[] = "urn:ogf:network:domain=";
        if ($allClients = $client->fetch(false)) {
            foreach ($allClients as $c) {
                if ($c->alias)
                    $hostArray[] = $c->alias;
                if ($c->ip_dcn)
                    $hostArray[] = $c->ip_dcn;
                if ($c->ip_internet)
                    $hostArray[] = $c->ip_internet;
                if ($c->mac_address)
                    $hostArray[] = $c->mac_address;
            }
        }


        // --------------------------------------
        // STEP 3 VARIABLES ---------------
        $bmin = 100;
        $bmax = 1000;
        $bdiv = 100;
        $bwarn = 0.7;
        // --------------------------------
        // STEP 4 VARIABLES --------------------------
        $dateFormat = "d/m/Y";
        $js_dateFormat = "dd/mm/yy";
        //$dateFormat = "M j, Y";
        //$js_dateFormat = "M d, yy";

        $hourFormat = "H:i";
        //$hourFormat = "g:i a";

        $hoursArray = array();
        for ($h = 0; $h < 24; $h++) {
            for ($m = 0; $m < 60; $m = $m + 30) {
                $hour = ($h < 10) ? "0$h" : $h;
                $min = ($m < 10) ? "0$m" : $m;
                $hoursArray[] = "$hour:$min";
            }
        }

        $today_check = DayofWeek();

        $lang = explode(".", Language::getInstance()->getLanguage());
        $js_lang = str_replace("_", "-", $lang[0]);
        // --------------------------------------------
        //if ($domToMapArray) {
        // Args to Script
        $this->setArgsToScript(array(
            // bandwidth
            "band_min" => $bmin,
            "band_max" => $bmax,
            "band_div" => $bdiv,
            "band_warning" => $bwarn,
            "warning_string" => _("Authorization from Network Administrator will be required."),
            // flash messages
            "flash_nameReq" => _("A name is required"),
            "flash_bandInv" => _("Invalid value for bandwidth"),
            "flash_sourceReq" => _("A source is required"),
            "flash_srcVlanInv" => _("Invalid value for source VLAN"),
            "flash_srcVlanReq" => _("Source VLAN type required"),
            "flash_destReq" => _("A destination is required"),
            "flash_dstVlanInv" => _("Invalid value for destination VLAN"),
            "flash_dstVlanReq" => _("Destination VLAN type required"),
            "flash_timerReq" => _("Timer is required"),
            "flash_timerInvalid" => _("The end time occurs before the start time"),
            "flash_invalidDuration" => _("Invalid duration"),
            "flash_missingEndpoints" => _("Missing endpoints"),
            "flash_sameSrcDst" => _("Source and destination endpoints cannot be the same"),
            "flash_couldNotGetHost" => _("Could not get host"),
            "flash_domainNotFound" => _("Domain not found"),
            "flash_deviceNotFound" => _("Device not found"),
            "flash_portNotFound" => _("Port not found"),
            "flash_pointNotSet" => _("Could not set point, probably there is not enough parameters"),
            "flash_deviceNotSet" => _("Device not set"),
            "flash_portNotSet" => _("Port not set"),
            "flash_pointCannotBeSource" => _("The point specified cannot be set as source"),
            "flash_deviceCannotBeSource" => _("Device cannot be set as source"),
            "flash_portCannotBeSource" => _("Port cannot be set as source"),
            // endpoints
            "domain_string" => _("Domain"),
            "domains_string" => _("Domains"),
            "network_string" => _("Network"),
            "networks_string" => _("Networks"),
            "device_string" => _("Device"),
            "devices_string" => _("Devices"),
            "from_here_string" => _("From here"),
            "to_here_string" => _("To here"),
            "cluster_information_string" => _("Information about cluster"),
            "coordinates_string" => _("Coordinates"),
            "any_string" => _("any"),
            "value_string" => _("Value"),
            "ok_string" => _("Ok"),
            "cancel_string" => _("Cancel"),
            // timers
            "date_format" => $js_dateFormat,
            "language" => $js_lang,
            "horas" => $hoursArray,
            "today" => $today_check,
            "repeat_every_string" => _("Repeat every"),
            "day_string" => _("day"),
            "days_string" => _("days"),
            "week_string" => _("week"),
            "weeks_string" => _("weeks"),
            "on_string" => _("on"),
            "month_string" => _("month"),
            "months_string" => _("months"),
            "year_string" => _("year"),
            "years_string" => _("years"),
            "hour_string" => _("hour"),
            "hours_string" => _("hours"),
            "minute_string" => _("minute"),
            "minutes_string" => _("minutes"),
            "and_string" => _("and"),
            "until_string" => _("until"),
            "times_string" => _("times"),
            "time_string" => _("time"),
            "end_rule_string" => _("Please set an end rule"),
            "select_day_string" => _("Select at least one day"),
            "set_name_string" => _("Set name"),
            "invalid_time_string" => _("Invalid time"),
            "active_string" => _("Active from"),
            "at_string" => _("at"),
            "reset_zoom" => _("Reset Zoom"),
            "any_string" => _("any"),
            "domains" => $domToMapArray,
            "hosts" => $hostArray
            //"urn_string" => $allUrns
        ));
        //}
        // ARGS to body ----------------------------------------------------------------
        $args = new stdClass();
        // arg name
        //$args->name = $name;
        // arg endpoints
        //$args->domains = $domains;
        // arg bandwidth
        //$args->bandwidthTip = "(" . $min . ", " . ($min + $div) . ", " . ($min + 2 * $div) . ", " . ($min + 3 * $div) . ", ... , " . $max . ")";
        // arg timer
        $args->start_date = date($dateFormat, (time() + 30 * 60));
        $args->finish_date = date($dateFormat, (time() + 90 * 60));
        $args->start_time = date($hourFormat, (time() + 30 * 60));
        $args->finish_time = date($hourFormat, (time() + 90 * 60));

        $this->setArgsToBody($args);
        // -----------------------------------------------------------------------------
        // SCRIPTS -----------------------------------------
        $this->addScriptForLayout(array(/*'googlemaps', 'StyledMarker', 'reservations', 'reservation_map', 'flows',*/'markerClusterer', 'timers', 'jquery.timePicker', 'reservations_add'/*, 'map_init'*/));
        
        if ($js_lang != "en-US") {
            $this->addScript("jquery.ui.datepicker-$js_lang");
        }

        $this->render('add');
    }
    
    public function selectThisHost() {
        $endpointObj = client_info::getBestEndpoint($_SERVER['REMOTE_ADDR']);
        $this->renderJson($endpointObj);
    }
    
    public function chooseHost() {
        $endpointObj = client_info::getBestEndpoint(Common::POST('edp_reference'));
        $this->renderJson($endpointObj);
    }

    public function submit() {
        $res_end_timestamp = microtime(true);
        $res_begin_timestamp = Common::getSessionVariable("res_begin_timestamp");
        $res_diff_timestamp = $res_end_timestamp - $res_begin_timestamp;

        CakeLog::write("circuits", "Reservation data POST".print_r($_POST,TRUE));

        /**
         * insere o flow
         */
        $flow_cont = new flows();
        $new_flow = $flow_cont->add();

        /**
         * insere o timer
         */
        $timer_cont = new timers();
        $new_timer = $timer_cont->add();

        if ($new_flow && $new_timer) {
            $reservation = new reservation_info();
            $reservation->res_name = Common::POST("res_name");
            $reservation->bandwidth = Common::POST("bandwidth");
            $reservation->flw_id = $new_flow->flw_id;
            $reservation->tmr_id = $new_timer->tmr_id;
            $reservation->creation_time = $res_diff_timestamp;
            $reservation->usr_id = AuthSystem::getUserId();
        } else {
            $this->setFlash(_('Fail to save endpoints or timer on database'), 'error');
            $this->show();
            return;
        }

        /**
         * 1- envia ao OSCARS como signal-xml todas as recorrências da reserva
         * 2- insere a tabela gri_info com send=0
         * 3- envia ao ode com o wsdl correspondente ao dominio origem
         * 4- o workflow é executado
         * 5- ao receber uma açao de notifyresponse, se for
         *  5.1- accept: atualiza o campo send=1, atualiza o status da requisicao
         *               em aceita.
         *  5.2- No momento de início da reserva, o daemon executa a
         *       função check e envia um createPath ao OSCARS.
         *       Atualiza o status da requisição para SENT TO OSCARS.
         *       O status dos gris agora correspondem o status da reserva no OSCARS
         *  5.2- reject: cancela a reserva no oscars, coloca o status da
         *       requisicao em denied e apaga os gris do banco do MEICAN.
         *       A reserva é finalizada.
         *
         */
        //buscar urn source para adicionar a reserva embaixo
        $urn = new urn_info();
        $urn->urn_string = $new_flow->src_urn_string;
        $src_urn = $urn->fetch(FALSE);

        //buscar urn destino para adicionar a reserva embaixo

        if ($res = $reservation->insert($src_urn[0]->urn_id, 'urn_info')) {
            $result = $this->send($res);
            switch ($result) {
                case 0:
                    $res->delete();
                    $new_flow->delete();
                    $new_timer->delete();
                    $this->setFlash(_('Error to send reservation to OSCARS'), 'error');
                    $this->show();
                    return;
                    break;
                case 1:
                    $this->setFlash(_('Reservation submitted'), 'success');
                    break;
                default:
                    $this->setFlash("$result " . _('reservations submitted'), 'success');
                    break;
            }

            $this->view(array("res_id" => $res->res_id, "refresh" => '1'));
        } else {
            $new_flow->delete();
            $new_timer->delete();
            $this->setFlash(_('Fail to save reservation on database'), 'error');
            $this->show();
        }
    }
    
    public function view2($param_array){
        $this->view($param_array);
    }

    public function view($param_array) {
        $resId = NULL;
        $refresh = 0;
        if (array_key_exists('res_id', $param_array)) {
            $resId = $param_array['res_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }
        if (array_key_exists('refresh', $param_array))
            $refresh = (integer) $param_array['refresh'];

        $res_info = new reservation_info();
        $res_info->res_id = $resId;
        $resResult = $res_info->fetch();

        if (!$resResult) {
            $this->setFlash(_("Reservation not found"), "fatal");
            $this->show();
            return;
        } else {
            $reservation = $resResult[0];
        }

        $flow_info = new flow_info();
        $flow_info->flw_id = $reservation->flw_id;
        $flow = $flow_info->getFlowDetails();

        $usr_info = new user_info();
        $usr_info->usr_id = $reservation->usr_id;
        $usr_login = $usr_info->get('usr_login', FALSE);
        
        $dom = new domain_info();
        if ($domain = $dom->getOSCARSDomain($flow->source->urn)) {
            $flow->source->domain = $domain->dom_descr;
        } else {
            $urn = new urn_info();
            $urn->urn_string = $flow->source->urn;
            $urn_id = $urn->get('urn_id');

            $urn_aco = new Acos($urn_id, "urn_info");
            $dev_aco = $urn_aco->getParentNodes();
            $net_aco = $dev_aco[0]->getParentNodes();
            $dom_aco = $net_aco[0]->getParentNodes();

            $dom = new domain_info();
            $dom->dom_id = $dom_aco[0]->obj_id;
            $flow->source->domain = $dom->get('dom_descr');
        }

        $dom = new domain_info();
        if ($domain = $dom->getOSCARSDomain($flow->dest->urn)) {
            $flow->dest->domain = $domain->dom_descr;
        } else {
            $urn = new urn_info();
            $urn->urn_string = $flow->dest->urn;
            $urn_id = $urn->get('urn_id');

            $urn_aco = new Acos($urn_id, "urn_info");
            $dev_aco = $urn_aco->getParentNodes();
            $net_aco = $dev_aco[0]->getParentNodes();
            $dom_aco = $net_aco[0]->getParentNodes();

            $dom = new domain_info();
            $dom->dom_id = $dom_aco[0]->obj_id;
            $flow->dest->domain = $dom->get('dom_descr');
        }
        
        if ($flow->path)
            $flow->path = MeicanTopology::getWaypoints($flow->path);
        
        if (!$flow) {
            $this->setFlash(_("Flow not found or could not get endpoints information"), "fatal");
            $this->show();
            return;
        }
        
        $timer_info = new timer_info();
        $timer_info->tmr_id = $reservation->tmr_id;
        $timer = $timer_info->getTimerDetails();

        if (!$timer) {
            $this->setFlash(_("Timer not found"), "fatal");
            $this->show();
            return;
        }

        $req = new request_info();
        $req->resource_id = $reservation->res_id;
        $req->resource_type = 'reservation_info';
        $req->answerable = 'no';

        $request = null;
        if ($result = $req->fetch()) {
            // a reserva possui requisição
            $request = new stdClass();
            $request->response = $result[0]->response;
            //$request->message = $result[0]->message;
            $request->status = $result[0]->status;
        }

        $status = array();

        $gri = new gri_info();

        if ($gris = $gri->getGrisToView($reservation->res_id)) {
            foreach ($gris as $g) {
                $stat_obj = new stdClass();
                $stat_obj->id = $g->id;
                $stat_obj->status = $g->original_status;
                $status[] = $stat_obj;
            }
        }

        $this->setArgsToScript(array(
            "refreshReservation" => $refresh,
            "reservation_id" => $reservation->res_id,
            "user_login" => $usr_login,
            "status_array" => $status,
            "src_lat_network" => $flow->source->latitude,
            "src_lng_network" => $flow->source->longitude,
            "dst_lat_network" => $flow->dest->latitude,
            "dst_lng_network" => $flow->dest->longitude,
            "reservation_path" => $flow->path,
            "domain_string" => _("Domain"),
            "domains_string" => _("Domains"),
            "network_string" => _("Network"),
            "networks_string" => _("Networks"),
            "device_string" => _("Device"),
            "devices_string" => _("Devices"),
            "from_here_string" => _("From Here"),
            "to_here_string" => _("To Here"),
            "cluster_information_string" => _("Information about cluster"),
            'str_error_refresh_status' => _("Error to get status")
        ));
        $this->set(array(
            'res_name' => $reservation->res_name,
            'res_id' => $reservation->res_id,
            'bandwidth' => $reservation->bandwidth
        ));
        $this->set(compact('gris', 'flow', 'timer', 
                'request', 'refresh', 'usr_login'));
        $this->addScriptForLayout(array('reservations', 'reservations_view'));
        $this->render('view');
    }

    /**
     * @todo Cancelar reservas no OSCARS (ativas e pendentes) antes de excluir do banco
     */
    public function delete($param_array) {
        if (array_key_exists('refresh', $param_array))
            $refresh = (integer) $param_array['refresh'];
        
        $del_reservations = Common::POST("del_checkbox");

        if ($del_reservations) {
            foreach ($del_reservations as $resId) {
                $gris_to_cancel = array();

                $reservation = new reservation_info();
                $reservation->res_id = $resId;

                $gri = new gri_info();
                $gri->res_id = $resId;
                if ($gris = $gri->fetch(FALSE)) {
                    foreach ($gris as $g) {
                        $g->delete(FALSE);
                        //$gris_to_cancel[] = $g->gri_descr;
                    }
                }

                if ($tmp = $reservation->fetch()) {
                    $result = $tmp[0];

                    $flow = new flow_info();
                    $flow->flw_id = $result->flw_id;
                    $flow->delete();

                    $timer = new timer_info();
                    $timer->tmr_id = $result->tmr_id;
                    $timer->delete();

                    //if ($client->cancel($gris_to_cancel)) {
                        if ($reservation->delete())
                            $this->setFlash(_("Reservation") . " '$result->res_name' " . _("deleted"), 'success');
                    //} else
                        //$this->setFlash(_("Reservation") . " '$result->res_name' " . _("could not be cancelled"), 'error');
                }
            }
        }

        if ($refresh)
            $this->status();
        else
            $this->history();
    }

    function query($reservation_info) {
        //descobrir IP do dominio origem da reserva para enviar ao OSCARS adequado
        $result = $reservation_info->fetch();
        $res = $result[0];
        $flow = new flow_info();
        $flow->flw_id = $res->flw_id;
        $flw = $flow->getFlowDetails();

        $gri = new gri_info();
        $gri->res_id = $reservation_info->res_id;
        $gris = $gri->fetch();

        foreach ($gris as $g) {
            $oscarsRes = new OSCARSReservation();
            $oscarsRes->setOscarsUrl($flw->source->idc_url);
            $oscarsRes->setGri($g->gri_descr);
            $oscarsRes->queryReservation();
            unset($oscarsRes);
        }
    }

    public function cancel($param_array) {
        $cancel_gris = Common::POST('cancel_checkbox');

        $gri = new gri_info();
        $gri->gri_id = $cancel_gris;
        $gris = $gri->fetch(FALSE);
        
        $cont = 0;

        if ($gris) {
            $dom = new domain_info();
            $dom->dom_id = $gris[0]->dom_id;

            if ($idc_url = $dom->get('idc_url')) {
                foreach ($gris as $g) {
                    if ($g->status == "ACTIVE" || $g->status == "PENDING" || $g->status == "ACCEPTED") {
                        $oscarsRes = new OSCARSReservation();
                        $oscarsRes->setOscarsUrl($idc_url);
                        $oscarsRes->setGri($g->gri_descr);
                        CakeLog::write("info", "GRI to cancel: ".print_r($g->gri_descr, TRUE));
                        /**
                         * @todo cancelar várias reservas de uma só vez
                         */
                        if ($oscarsRes->cancelReservation()) {
                            $cont++;
                            $status_ret = $oscarsRes->getStatus();
                            if ($status_ret != $g->status) {
                                $gri_tmp = new gri_info();
                                $gri_tmp->gri_id = $g->gri_id;
                                $gri->updateTo(array('status' => $status_ret), FALSE);
                            }
                        }
                    }
                }
            }
        }
        
        switch ($cont) {
            case 0:
                $this->setFlash(_("No reservation was cancelled"), "warning");
                break;
            case 1:
                $this->setFlash(_("One reservation was cancelled"), "success");
                break;
            default:
                $this->setFlash("$cont " . _("reservations were cancelled"), "success");
                break;
        }
        
        sleep(3);
        $this->view($param_array);
    }

    function listStatus($grisArray) {
        $oscarsRes = new OSCARSReservation();
        $oscarsRes->setOscarsUrl("200.132.1.28:8080"); //oscars2
        $oscarsRes->setGrisString($grisArray);
        $result = $oscarsRes->listReservations();
        debug("result do list", $result);
    }

    function send($reservation_info) {
        
        CakeLog::write("circuits", "Reservation to be sent:\n".print_r($reservation_info,TRUE));
        
        $flow_info = new flow_info();
        $flow_info->flw_id = $reservation_info->flw_id;
        $flow_res = $flow_info->fetch();
        $flow = $flow_res[0];
        
        $src_urn_string = $flow->src_urn_string;

        $domain = new domain_info();
        $src_dom = $domain->getOSCARSDomain($src_urn_string);

        $oscarsRes = new OSCARSReservation();
        $oscarsRes->setOscarsUrl($src_dom->idc_url);
        $oscarsRes->setDescription($reservation_info->res_name);
        $oscarsRes->setBandwidth($reservation_info->bandwidth);
        $oscarsRes->setSrcEndpoint($flow->src_urn_string);
        $oscarsRes->setDestEndpoint($flow->dst_urn_string);

        if ($path = $flow->path)
            $oscarsRes->setPath($path);

        if ($flow->src_vlan !== null) {
            $flow->src_vlan = (integer) $flow->src_vlan;
            if ($flow->src_vlan === 0)
                $oscarsRes->setSrcIsTagged(false);
            else {
                $oscarsRes->setSrcIsTagged(true);
                $oscarsRes->setSrcTag($flow->src_vlan);
            }
        }

        if ($flow->dst_vlan !== null) {
            $flow->dst_vlan = (integer) $flow->dst_vlan;
            if ($flow->dst_vlan === 0)
                $oscarsRes->setDestIsTagged(false);
            else {
                $oscarsRes->setDestIsTagged(true);
                $oscarsRes->setDestTag($flow->dst_vlan);
            }
        }

        //precisa descobrir se a reserva deve ou não ser enviada para AUTORIZAÇÃO
        if ($src_dom->ode_ip && $src_dom->ode_wsdl_path && $src_dom->ode_start) {
            //irá para autorização
            //cria reserva do tipo signal-xml
            $oscarsRes->setPathSetupMode('timer-automatic');
        } else
            $oscarsRes->setPathSetupMode('timer-automatic');

        $tim = new timer_info();
        $tim->tmr_id = $reservation_info->tmr_id;
        $timer = $tim->get();
        $arrayRec = $timer->getRecurrences();

        $resSent = 0;
        foreach ($arrayRec as $t) {
            $tmp = $oscarsRes;
            $tmp->setStartTimestamp($t->start); //em timestamp
            $tmp->setEndTimestamp($t->finish);
            
            CakeLog::write("circuits", "Sending reservation:\n".print_r($tmp,TRUE));

            if ($tmp->createReservation()) {
                $resSent++;

                $new_gri = new gri_info();
                $new_gri->gri_descr = $tmp->getGri();
                $new_gri->status = $tmp->getStatus();
                $new_gri->res_id = $reservation_info->res_id;
                $new_gri->dom_id = $src_dom->dom_id;

                $date = new DateTime();
                $date->setTimestamp($t->start);
                $new_gri->start = $date->format('Y-m-d H:i');
                $date->setTimestamp($t->finish);
                $new_gri->finish = $date->format('Y-m-d H:i');

                $new_gri->send = "0"; //para as reservas sem autorização do tipo
                //timer-automatic o daemon nao precisa enviar o createPath

                $new_gri->insert();
            }
            unset($tmp);
        }
        
        //para buscar o dst_ode_ip
        $dst_urn_string = $flow->dst_urn_string;

        $domain = new domain_info();
        $dst_dom = $domain->getOSCARSDomain($dst_urn_string);

        if ($resSent && $src_dom->ode_ip && $src_dom->ode_wsdl_path && $src_dom->ode_start && $dst_dom->ode_ip) {
            //cria nova request com o domínio $src_dom
            $newReq = new request_info();
            
            $newReq->req_id = $newReq->getNextId('req_id');
            
            $newReq->src_ode_ip = $src_dom->ode_ip;
            $newReq->src_usr = $reservation_info->usr_id;

            $newReq->dst_ode_ip = $dst_dom->ode_ip;

            $newReq->resource_type = 'reservation_info';
            $newReq->resource_id = $reservation_info->res_id;
            
            $newReq->answerable = 'no';
            
            $newReq->response = NULL;
            $newReq->message = NULL;
            
            $newReq->crr_ode_ip = NULL;
            $newReq->response_user = NULL;
            $newReq->start_time = NULL;
            $newReq->finish_time = NULL;
            
            $requestSOAP = array(
                'req_id' => $newReq->req_id,
                'src_ode_ip' => $newReq->src_ode_ip,
                'dst_ode_ip' => $newReq->dst_ode_ip,
                'usr_src' => $newReq->src_usr);

            CakeLog::write("circuits","Sending for authorization:\n". print_r($requestSOAP,TRUE));
            try {
                $client = new SoapClient($src_dom->ode_wsdl_path, array('cache_wsdl' => WSDL_CACHE_NONE));
                
                $client->{$src_dom->ode_start}($requestSOAP);

                //$client->__soapCall($src_dom->ode_start, $requestSOAP);

                $newReq->status = 'SENT FOR AUTHORIZATION';

                if ($newReq->insert())
                    return $resSent;
                else {
                    CakeLog::write("error", "Failed to save request");
                    return FALSE;
                }
                    
            } catch (Exception $e) {
                CakeLog::write("error", "Caught exception while trying to connect to ODE:\n". print_r($e->getMessage()));
                $this->setFlash("error", _('Error at invoking business layer.'));
                $newReq->status = 'SENT FOR AUTHORIZATION';

                $newReq->insert();
                return FALSE;
            }
        } else
            return $resSent;
    }

    public function check() {
        $this->autoRender = false;
        CakeLog::write("debug", "check chegou no controller");
        $gris = gri_info::getGrisToCreatePath();
        CakeLog::write("debug", print_r($gris, true));

        if ($gris) {
            foreach ($gris as $g) {
                $dom = new domain_info();
                $dom->dom_id = $g->dom_id;
                $domain = $dom->fetch(false);

                $oscars_reservation = new OSCARSReservation();
                $oscars_reservation->setOscarsUrl($domain[0]->idc_url);
                $oscars_reservation->setGri($g->gri_descr);
                
                CakeLog::write("debug","oscars res class\n".print_r($oscars_reservation,true));

                //if (true) {
                if ($oscars_reservation->createPath()) {
                    CakeLog::write("debug","Create path successful\n".print_r($g,true));
                    $status = $oscars_reservation->getStatus();
                    //$status="INCREATE";
                    $g->updateTo(array("send" => "0", "status" => $status), false);
                }
            }
        }
    }

}

?>
