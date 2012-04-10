<?php

//include_once 'libs/Model/resource_model.php';
include_once 'libs/Model/model.php';

class gri_info extends Model {

    public function gri_info() {
        $this->setTableName("gri_info");

        // Add all table attributes
        $this->addAttribute("gri_id", "INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("gri_descr", "VARCHAR");
        $this->addAttribute("status", "VARCHAR");
        $this->addAttribute("start", "VARCHAR");
        $this->addAttribute("finish", "VARCHAR");
        $this->addAttribute("dom_id", "INTEGER");
        $this->addAttribute("res_id", "INTEGER");
        $this->addAttribute("send", "INTEGER");
    }
    
    /**
     * @return Array res_id
     */
    public function getStatusResId($domId=NULL) {
        $this->status = array("ACTIVE", "PENDING", "ACCEPTED", "SUBMITTED", "INCREATE", "INSETUP", "INTEARDOWN", "INMODIFY", "");
        if ($domId)
            $this->dom_id = $domId;
        
        $filteredArray = array(0);
        if ($gri_res = $this->fetch(FALSE)) {
            $filter_res_id_array = Common::arrayExtractAttr($gri_res, "res_id");
            $filteredArray = array_unique($filter_res_id_array);
        }
        
        return $filteredArray;
    }
    
    /**
     * @return Array res_id
     */
    public function getHistoryResId() {
        $this->status = array("FAILED", "FINISHED", "CANCELLED");

        $filteredArray = array(0);
        if ($hist_gri_res = $this->fetch(FALSE)) {

            $hist_filter_res_id_array = Common::arrayExtractAttr($hist_gri_res, "res_id");
            $stat_gri_res = $this->getStatusResId();

            // faz a diferença dos arrays, para garantir que a reserva não tenha nenhum GRI que mude de status
            $filter_res_id_array = array_diff($hist_filter_res_id_array, $stat_gri_res);
            $filteredArray = array_unique($filter_res_id_array);
        }

        return $filteredArray;
    }
    
    public function getGrisToView($res_id = null, $getAvailableBandwidth = false) {
        $gri = new gri_info();

        $request = null;
        if ($res_id) {
            $gri->res_id = $res_id;

            $req = new request_info();
            $req->resource_id = $res_id;
            $req->resource_type = 'reservation_info';
            $req->answerable = 'no';
            
            if ($result = $req->fetch(false)) {
                // a reserva possui requisição
                $request = new stdClass();
                $request->response = $result[0]->response;
                $request->status = $result[0]->status;
            }
        }

        $gris = array();

        if ($allGris = $gri->fetch(FALSE)) {
            $dateFormat = "d/m/Y";
            //$dateFormat = "M j, Y";

            $hourFormat = "H:i";
            //$hourFormat = "g:i a";
            foreach ($allGris as $g) {
                $gri = new stdClass();
                $gri->id = $g->gri_id;
                $gri->descr = $g->gri_descr;

                if ($request && $request->response != 'accept') {
                    // show request status
                    if ($request->response == 'reject') {
                        // reservation request was denied
                        $gri->status = gri_info::translateStatus('REJECTED');
                        $gri->original_status = 'REJECTED';
                    } else {
                        // reservation request is pending
                        $status = ($request->status) ? $request->status : "UNKNOWN";
                        $gri->status = gri_info::translateStatus($status);
                        $gri->original_status = "REQ_PENDING";
                    }
                } else {
                    // request doesn't exist or reservation request was accepted => show GRI status
                    $gri->status = gri_info::translateStatus($g->status);
                    $gri->original_status = $g->status;
                }
                
                $start = new DateTime($g->start);
                $finish = new DateTime($g->finish);

                $gri->start = $start->format("$dateFormat $hourFormat");
                $gri->finish = $finish->format("$dateFormat $hourFormat");
                
                $gri->start_date = $g->start;
                $gri->finish_date = $g->finish;
                
                if ($getAvailableBandwidth) {
                    $bands = reservation_info::getAvailableBandwidth($res_id, $g->gri_id);
                    $gri->available_bandwidth = $bands[0];
                }

                $gris[] = $gri;
            }
        }
        return $gris;
    }
    
    static public function getGrisToCreatePath() {
        $sql = "SELECT `gri_id`, `gri_descr`, `dom_id` FROM `gri_info`";
        $sql .= " WHERE `send`=1 AND `status`='PENDING' AND NOW() BETWEEN `start` AND `finish`";
        return parent::querySql($sql, 'gri_info');
    }
    
    static public function getGrisToCalendar($start, $finish, $res_id = null) {
        if ($start && $finish) {
            $sql = "SELECT `gi`.`gri_id`, `gi`.`gri_descr`, `gi`.`status`, `gi`.`start` AS 'start_date', `gi`.`finish` AS 'finish_date', `ri`.`bandwidth` FROM `gri_info` AS `gi`";
            $sql .= " LEFT JOIN `reservation_info` AS `ri` ON `gi`.`res_id`=`ri`.`res_id`";
            $sql .= " WHERE";
            $sql .= " ((`gi`.`start` BETWEEN '$start' AND '$finish') OR";
            $sql .= " (`gi`.`finish` BETWEEN '$start' AND '$finish'))";
            $sql .= " AND (`gi`.`status` NOT IN ('FAILED', 'FINISHED', 'CANCELLED', ''))";
            $sql .= " AND (`gi`.`status` IS NOT NULL)";
            $sql .= ($res_id) ? " AND (`gi`.`res_id` != $res_id);" : ";";
            return parent::querySql($sql, 'gri_info');
        } else
            return array();
    }
    
    static public function getConflictedGris($start, $finish, $res_id = null) {
        if ($start && $finish) {
            $sql = "SELECT `gi`.*, `ri`.`bandwidth` FROM `gri_info` AS `gi`";
            $sql .= " LEFT JOIN `reservation_info` AS `ri` ON `gi`.`res_id`=`ri`.`res_id`";
            $sql .= " WHERE";
            $sql .= " !((`gi`.`finish` <= '$start') OR (`gi`.`start` >= '$finish'))";
            $sql .= " AND (`gi`.`status` NOT IN ('FAILED', 'FINISHED', 'CANCELLED', ''))";
            $sql .= " AND (`gi`.`status` IS NOT NULL)";
            $sql .= ($res_id) ? " AND (`gi`.`res_id` != $res_id);" : ";";
            return parent::querySql($sql, 'gri_info');
        } else
            return array();
    }
    
    static public function translateStatus($newStatus) {
        $status = "";
        switch ($newStatus) {
            case "ACTIVE":
                $status = _("Active");
                break;
            case "PENDING":
                $status = _("Scheduled");
                break;
            case "FINISHED":
                $status = _("Finished");
                break;
            case "CANCELLED":
                $status = _("Cancelled");
                break;
            case "FAILED":
                $status = _("Failed");
                break;
            case "ACCEPTED":
                $status = _("Accepted");
                break;
            case "SUBMITTED":
                $status = _("Submitted");
                break;
            case "INCREATE":
                $status = _("In create");
                break;
            case "INSETUP":
                $status = _("In setup");
                break;
            case "INTEARDOWN":
                $status = _("In tear down");
                break;
            case "INMODIFY":
                $status = _("In modify");
                break;
            case "REJECTED":
                $status = _("Rejected");
                break;
            case "UNKNOWN":
                $status = _("Unknown status");
                break;
            case "NO_GRI":
                $status = _("Reservation has no GRI");
                break;
            default:
                $status = $newStatus;
        }
        return $status;
    }

}

?>