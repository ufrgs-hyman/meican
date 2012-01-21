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
include_once 'apps/circuits/models/oscars_reservation.php';

include_once 'apps/bpm/models/request_info.php';

include_once 'apps/topology/models/domain_info.php';
include_once 'apps/topology/models/topology.php';
include_once 'includes/nuSOAP/lib/nusoap.php';

class reservations extends Controller {

    public function reservations() {
        $this->app = 'circuits';
        $this->controller = 'reservations';
        $this->defaultAction = 'show';
    }

    public function show($filterArray=array()) {
        // inicializa variável de sessão
        Common::destroySessionVariable('res_begin_timestamp');

        $res_info = new reservation_info();
        if ($filterArray)
            $res_info->res_id = $filterArray;
        
        $allReservations = $res_info->fetch();
        if ($allReservations) {
            $reservations = array();
            $src_domains = array();

            foreach ($allReservations as $r) {
                $res = new stdClass();
                $res->id = $r->res_id;
                $res->name = $r->res_name;
                $res->bandwidth = $r->bandwidth;
                
                $status = $r->getStatus();
                $res->status = gri_info::translateStatus($status);

                $flow = new flow_info();
                $flow->flw_id = $r->flw_id;
                $res->flow = $flow->getFlowDetails();

                $timer = new timer_info();
                $timer->tmr_id = $r->tmr_id;
                $res->timer = $timer->getTimerDetails();

                $dom = new domain_info();
                if ($domain = $dom->getOSCARSDomain($res->flow->source->urn)) {
                    $res->flow->source->domain = $domain->dom_descr;
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

                $this->setInlineScript('reservations_init');
            }
            
            $args = new stdClass();
            $args->reservations = $reservations;
            $args->refresh = ($this->action == 'status') ? 1 : 0;
            
            $this->setAction('show');

            $this->setArgsToBody($args);
        } else {
            $args = new stdClass();
            $args->title = ($this->action == 'status') ? _("Active and pending reservations") : _("History reservations");
            $args->message = ($this->action == 'status') ? _("You have no active or pending reservation, try <a href='history'>history</a> or click the button below to create a <a href='add'>new</a> one")
                    : _("You have no reservation in history, click the button below to create a <a href='add'>new</a> one");
            $args->link = array("action" => "add");
            $this->setArgsToBody($args);
            
            $this->setAction('empty');
        }

        $this->render();
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
        $this->setAction("ajax");
        $this->setLayout("empty");

        $dom_id = Common::POST('dom_id');
        
        $gris = new gri_info();
        $resToRefresh = $gris->getStatusResId($dom_id);
        
        Framework::debug("res array to refresh",$resToRefresh);
        
        $res_info = new reservation_info();
        $res_info->res_id = $resToRefresh;
        
        $reservations = $res_info->fetch();
        
        //Framework::debug("res to refresh",$reservations);

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
            Framework::debug("Falha ao buscar reservas no refresh status");
            $this->setArgsToBody(FALSE);
            $this->render();
            return;
        }
        
        /**
         * Realiza a consulta com o vetor preenchido
         */
        $statusResult = array();
        if ($griList) {
            $dom = new domain_info();
            $dom->dom_id = $dom_id;
            $oscars_ip = $dom->get('oscars_ip');
            
            Framework::debug("gri list ro refresh", $griList);

            $oscarsRes = new OSCARSReservation();
            $oscarsRes->setOscarsUrl($oscars_ip);
            $oscarsRes->setGrisString($griList);

            if ($oscarsRes->listReservations()) {
                $statusResult = $oscarsRes->getStatusArray();
            } else {
                Framework::debug("Falha ao conectar OSCARS ($oscars_ip) no refresh status");
                $this->setArgsToBody(FALSE);
                $this->render();
                return;
            }
        }

        if (count($statusResult) != count($griList)) {
            Framework::debug("Problema de consistencia na refresh status", $statusResult);
            $this->setArgsToBody(FALSE);
            $this->render();
            return;
        }

        //Framework::debug("result list", $statusResult);

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
            $status_obj->name = $status;
            $status_obj->translate = gri_info::translateStatus($status);
            $status_obj->id = $res->res_id;
            
            $statusList[] = $status_obj;
        }

        //Framework::debug('status', $statusList);
        //echo json_encode($statusList);
        $this->setArgsToBody($statusList);
        $this->render();
    }

    public function gri_refresh_status() {
        $this->setAction("ajax");
        $this->setLayout("empty");

        $res_id = Common::POST("res_id");
        Framework::debug("gri stats",$res_id);

        $gri = new gri_info();
        $gri->res_id = $res_id;
        $gris = $gri->fetch(FALSE);
        if ($gris) {
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

            $statusList = array();

            if ($griList) {
                $dom = new domain_info();
                $dom->dom_id = $gris[0]->dom_id;
                $oscars_ip = $dom->get('oscars_ip');

                $oscarsRes = new OSCARSReservation();
                $oscarsRes->setOscarsUrl($oscars_ip);
                $oscarsRes->setGrisString($griList);

                if ($oscarsRes->listReservations()) {
                    $statusResult = $oscarsRes->getStatusArray();
                } else {
                    Framework::debug("Falha ao conectar OSCARS no refresh status");
                    $this->setArgsToBody(FALSE);
                    $this->render();
                    return;
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
                    $status_obj->name = $g->status;
                    $status_obj->translate = gri_info::translateStatus($g->status);
                    $statusList[] = $status_obj;

                    $ind++;
                }
            }

            $this->setArgsToBody($statusList);
            $this->render();
        } else {
            Framework::debug("Falha ao buscar gris no refresh status");
            $this->setArgsToBody(FALSE);
            $this->render();
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
        $allUrns = array();
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
                $urn = MeicanTopology::getURNs($d->dom_id);
                //Framework::debug("tempo", (microtime(true) - $before));

                foreach ($urn as $u) {
                    $allUrns[] = $u->urn_string;
                }
                $domToMapArray[] = $domain;
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

        $lang = explode(".", Language::getLang());
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
            "domains" => $domToMapArray,
            "urn_string" => $allUrns
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
        $args->start_date = date($dateFormat);
        $args->finish_date = date($dateFormat);
        $args->start_time = date($hourFormat, (time() + 30 * 60));
        $args->finish_time = date($hourFormat, (time() + 90 * 60));

        $this->setArgsToBody($args);
        // -----------------------------------------------------------------------------
        // SCRIPTS -----------------------------------------
        $this->addScriptForLayout(array(/*'googlemaps', 'markerClusterer', 'StyledMarker', 'map', 'reservations', 'reservation_map', 'reservations_add', 'flows', 'timers', 'jquery.timePicker',*/ 'reservations_add'/*, 'map_init'*/));
        //$this->setInlineScript('reservations_add_init');

        if ($js_lang != "en-US") {
            $this->addScript("jquery.ui.datepicker-$js_lang");
        }
        // -------------------------------------------------
        // ACTION ---------------------
        $this->setAction('add');
        // ----------------------------

        $this->render();
    }

    public function submit() {

        $res_end_timestamp = microtime(true);
        $res_begin_timestamp = Common::getSessionVariable("res_begin_timestamp");
        $res_diff_timestamp = $res_end_timestamp - $res_begin_timestamp;

//        Framework::debug("post", $_POST);
//        $this->add_form();
//        return;

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
        $result = $res_info->fetch();

        if ($result === FALSE) {
            $this->setFlash(_("Reservation not found"), "fatal");
            $this->show();
            return;
        } else {
            $reservation = $result[0];
        }

        $flow_info = new flow_info();
        $flow_info->flw_id = $reservation->flw_id;
        $flow = $flow_info->getFlowDetails();

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

        Framework::debug("flow", $flow);
        Framework::debug("timer", $timer);

        $req = new request_info();
        $req->resource_id = $reservation->res_id;
        $req->resource_type = 'reservation_info';
        $req->answerable = 'no';

        $request = NULL;
        if ($result = $req->fetch()) {
            // a reserva possui requisição
            $request = new stdClass();
            $request->response = $result[0]->response;
            $request->message = $result[0]->message;
            $request->status = $result[0]->status;
        }

        $status = array();
        $gris = array();

        $gri = new gri_info();
        $gri->res_id = $reservation->res_id;
        $allGris = $gri->fetch(FALSE);

        $dateFormat = "d/m/Y";
        //$dateFormat = "M j, Y";

        $hourFormat = "H:i";
        //$hourFormat = "g:i a";

        if ($allGris) {
            foreach ($allGris as $g) {
                $gri = new stdClass();
                $gri->id = $g->gri_id;
                $gri->descr = $g->gri_descr;
                $gri->status = gri_info::translateStatus($g->status);

                $stat_obj = new stdClass();
                $stat_obj->status = $g->status;
                $stat_obj->id = $g->gri_id;
                $status[] = $stat_obj;

                $start = new DateTime($g->start);
                $finish = new DateTime($g->finish);

                $gri->start = $start->format("$dateFormat $hourFormat");
                $gri->finish = $finish->format("$dateFormat $hourFormat");

                $gris[] = $gri;
            }
        }

        $this->setArgsToScript(array(
            "refreshReservation" => $refresh,
            "reservation_id" => $reservation->res_id,
            "status_array" => $status,
            "src_lat_network" => $flow->source->latitude,
            "src_lng_network" => $flow->source->longitude,
            "dst_lat_network" => $flow->dest->latitude,
            "dst_lng_network" => $flow->dest->longitude,
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

        $this->setInlineScript('reservations_view');

        $args = new stdClass();
        $args->gris = $gris;
        $args->flow = $flow;
        $args->timer = $timer;
        $args->res_name = $reservation->res_name;
        $args->bandwidth = $reservation->bandwidth;
        $args->res_id = $reservation->res_id;
        $args->request = $request;
        $args->refresh = $refresh;

        $this->setAction('view');
        $this->setArgsToBody($args);
        $this->render();
    }

    public function delete() {
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

        $this->show();
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
            $oscarsRes->setOscarsUrl($flw->source->oscars_ip);
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

            if ($oscars_ip = $dom->get('oscars_ip')) {
                foreach ($gris as $g) {
                    if ($g->status == "ACTIVE" || $g->status == "PENDING" || $g->status == "ACCEPTED") {
                        $oscarsRes = new OSCARSReservation();
                        $oscarsRes->setOscarsUrl($oscars_ip);
                        $oscarsRes->setGri($g->gri_descr);
                        Framework::debug("gri to cancel",$g->gri_descr);
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
        Framework::debug("result do list", $result);
    }

    function send($reservation_info) {
        
        Framework::debug("res send",$reservation_info);

        $flw_id = $reservation_info->flw_id;

        $flow = new flow_info();
        $flow->flw_id = $flw_id;
        $src_urn_string = $flow->get('src_urn_string');

        $domain = new domain_info();
        $src_dom = $domain->getOSCARSDomain($src_urn_string);

        $oscarsRes = new OSCARSReservation();
        $oscarsRes->setOscarsUrl($src_dom->oscars_ip);
        $oscarsRes->setDescription($reservation_info->res_name);
        $oscarsRes->setBandwidth($reservation_info->bandwidth);
        $oscarsRes->setSrcEndpoint($flow->get('src_urn_string'));
        $oscarsRes->setDestEndpoint($flow->get('dst_urn_string'));

        if ($path = $flow->get('path'))
            $oscarsRes->setPath($path);

        if ($vsrc = $flow->get('src_vlan'))
            if ($vsrc == 0)
                $oscarsRes->setSrcIsTagged(false);
            else {
                $oscarsRes->setSrcIsTagged(true);
                $oscarsRes->setSrcTag($vsrc);
            }

        if ($vdst = $flow->get('dst_vlan'))
            if ($vdst == 0)
                $oscarsRes->setDestIsTagged(false);
            else {
                $oscarsRes->setDestIsTagged(true);
                $oscarsRes->setDestTag($vdst);
            }

        //precisa descobrir se a reserva deve ou não ser enviada para AUTORIZAÇÃO
        if ($src_dom->ode_ip && $src_dom->ode_wsdl_path) {
            //irá para autorização
            //cria reserva do tipo signal-xml
            $oscarsRes->setPathSetupMode('signal-xml');
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

            Framework::debug("tmp", $tmp);

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

        if ($src_dom->ode_ip && $src_dom->ode_wsdl_path) {
            //cria nova request com o domínio dom_src
            $newReq = new request_info();
            $newReq->src_dom = $src_dom->dom_id;
            $newReq->req_id = $newReq->getNextId('req_id');

            //para buscar o dom_dst_ip
            $domain->dom_id = $flow->get('dst_dom');
            $dst_dom = $domain->get();
            $newReq->dst_dom = $dst_dom->dom_id;

            $newReq->src_usr = AuthSystem::getUserId();

            $newReq->resource_type = 'reservation_info';
            $newReq->resource_id = $reservation_info->res_id;
            $newReq->answerable = 'no';

            /**
             * PARA UM ÚNICO WSDL COM OPERAÇÕES SEPARADAS E PADRONIZADAS PARA ENVIAR
             * REQUISIÇÃO E ENVIAR RESPOSTA
             */
            $businessEndpoint = "http://$src_dom->ode_ip/$src_dom->ode_wsdl_path";

            $requestSOAP = array(
                'req_id' => $newReq->req_id,
                'dom_src_ip' => $src_dom->oscars_ip,
                'dom_dst_ip' => $dst_dom->oscars_ip,
                'usr_src' => $newReq->src_usr);

            Framework::debug('ira enviar para autorizaçao...', $requestSOAP);
            try {
                $client = new SoapClient($businessEndpoint, array('cache_wsdl' => 0));

                $client->startWorkflow($requestSOAP);

                $newReq->status = 'SENT FOR AUTHORIZATION';

                $newReq->insert();

                return TRUE;
            } catch (Exception $e) {
                Framework::debug("Caught exception: ", $e->getMessage());
                $this->setFlash(_('Error at invoking business layer.'));
                $newReq->status = 'SENT FOR AUTHORIZATION';

                $newReq->insert();
                return FALSE;
            }
        } else
            return $resSent;
    }

    public function check() {
        Framework::debug("chegou no controller");
        $gris = new gri_info();
        $all = $gris->fetch(FALSE);
        Framework::debug("gris", $all);

        foreach ($all as $g) {
            if ($g->send)
                if ($g->status == "PENDING") {
                    $now = time();
                    $start = new DateTime($g->start);
                    if ($start->getTimestamp() >= $now) {
                        $reservation_info = new reservation_info();
                        $reservation_info->res_id = $g->res_id;
                        $flw_id = $reservation_info->get('flw_id');
                        $flow = new flow_info();
                        $flow->flw_id = $flw_id;
                        $dom_src_id = $flow->get('src_dom');
                        $domain = new domain_info();
                        $domain->dom_id = $dom_src_id;
                        $src_dom = $domain->get();
                        $oscars_reservation = new OSCARSReservation();
                        $oscars_reservation->setOscarsUrl($src_dom->oscars_ip);

                        $oscars_reservation->setGri($g->gri_descr);
                        if ($oscars_reservation->createPath()) {
                            $status = $oscars_reservation->getStatus();
                            $g->updateTo(array("send" => "0", "status" => $status), FALSE);
                        }
                    }
                }
        }
    }

}

?>
