<?php

include_once 'apps/circuits/models/reservation_info.inc';
include_once 'apps/circuits/models/flow_info.inc';
include_once 'apps/circuits/models/timer_info.inc';
include_once 'apps/circuits/models/gri_info.inc';

class oscars {
    private $endpoint;
    private $res_id = NULL;

    public function oscars($res_id) {
        $this->endpoint = "http://".Framework::$bridgeIp."/axis2/services/BridgeOSCARS?wsdl";
        $this->res_id = $res_id;
    }

    public function createReservation() {
        if (!$this->res_id)
            return FALSE;

        $res = new reservation_info();
        $res->res_id = $this->res_id;
        $result = $res->fetch();
        if (!$result)
            return FALSE;

        $flow = new flow_info();
        $flow->flw_id = $result[0]->flow_id;
        $f_result = $flow->fetch(FALSE);

        $timer = new timer_info();
        $timer->tmr_id = $result[0]->tmr_id;
        $ts = NULL;
        if ($t_result = $timer->fetch())
            $ts = $t_result[0]->getRecurrences();

        $array_to_create = array();
        $array_to_create['args0'] = array();

        if ($ts && $f_result) {
            foreach ($ts as $t) {
                $array_to_create['args0'][] = $f_result[0]->src_urn_string; // source
                $array_to_create['args0'][] = $f_result[0]->dst_urn_string; // dest
                $array_to_create['args0'][] = $f_result[0]->bandwidth; // banda
                $array_to_create['args0'][] = $t->start; // begin TS
                $array_to_create['args0'][] = $t->finish; // end TS
            }
        } else
            return FALSE;

        if ($client = new SoapClient($this->endpoint, array('cache_wsdl' => 0)))
            if ($result = $client->create($array_to_create)) {
                Framework::debug('creating reservation', $result);
                if (is_array($result->return)) {
                    foreach ($result->return as $gri) {

                        $new_gri = new gri_info();
                        $new_gri->gri_id = $gri;
                        $new_gri->res_id = $this->res_id;
                        if ($q_result = $client->query(array($gri))) {
                            $new_gri->status = $q_result->return[0];
                            $date = new DateTime();
                            $date->setTimestamp($q_result->return[1]);
                            $new_gri->start = $date->format('Y-m-d H:i');
                            $date->setTimestamp($q_result->return[2]);
                            $new_gri->finish = $date->format('Y-m-d H:i');
                        }

                        $new_gri->insert();
                    }

                    return TRUE;
                } else { //apenas uma reserva
                    $new_gri = new gri_info();
                    $new_gri->gri_id = $result->return;
                    $new_gri->res_id = $this->res_id;
                    if ($q_result = $client->query(array($result->return))) {
                        $new_gri->status = $q_result->return[0];
                        $date = new DateTime();
                        $date->setTimestamp($q_result->return[1]);
                        $new_gri->start = $date->format('Y-m-d H:i');
                        $date->setTimestamp($q_result->return[2]);
                        $new_gri->finish = $date->format('Y-m-d H:i');
                    }


                    if ($new_gri->insert()) {
                        return TRUE;
                    }
                }

            }
    }

}

?>
