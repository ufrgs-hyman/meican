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
    
    public function getGrisToView($res_id = null) {
        $gri = new gri_info();

        if ($res_id)
            $gri->res_id = $res_id;

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
                $gri->status = gri_info::translateStatus($g->status);
                $gri->original_status = $g->status;

                $start = new DateTime($g->start);
                $finish = new DateTime($g->finish);

                $gri->start = $start->format("$dateFormat $hourFormat");
                $gri->finish = $finish->format("$dateFormat $hourFormat");
                
                $gri->start_date = $g->start;
                $gri->finish_date = $g->finish;

                $gris[] = $gri;
            }
        }
        return $gris;
    }
    
    public function getGrisToCreatePath() {
        echo "gris";
        $sql = "SELECT gri_id, gri_descr, dom_id FROM `gri_info`";
        $sql .= " WHERE `send`=1 AND `status`='PENDING' AND NOW() BETWEEN `start` AND `finish`";
        return parent::querySql($sql, 'gri_info');
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