<?php

//include_once 'libs/resource_model.php';
include_once 'libs/model.php';

class gri_info extends Model {

    public function gri_info() {
        $this->setTableName("gri_info");

        // Add all table attributes
        $this->addAttribute("gri_id", "VARCHAR", TRUE, TRUE, FALSE);
        $this->addAttribute("dom_id", "INTEGER");
        $this->addAttribute("status", "VARCHAR");
        $this->addAttribute("res_id", "INTEGER");
        $this->addAttribute("start", "VARCHAR");
        $this->addAttribute("finish", "VARCHAR");
        $this->addAttribute("send", "INTEGER");
    }
    
    /**
     * @return Array res_id
     */
    public function getStatusResId($domId=NULL) {
        $this->status = array("ACTIVE", "PENDING", "ACCEPTED", "SUBMITTED", "INCREATE", "INSETUP", "INTEARDOWN", "INMODIFY");
        if ($domId)
            $this->dom_id = $domId;
        $gri_res = $this->fetch(FALSE);
        
        $filter_res_id_array = array();
        if ($gri_res) {
            foreach ($gri_res as $g) {
                $filter_res_id_array[] = $g->res_id;
            }
        }
        $filteredArray = array_unique($filter_res_id_array);
        return $filteredArray;
    }
    
    /**
     * @return Array res_id
     */
    public function getHistoryResId() {
        $this->status = array("FAILED", "FINISHED", "CANCELLED", "");
        $his_gri_res = $this->fetch(FALSE);
        
        $his_filter_res_id_array = array();
        if ($his_gri_res) {
            foreach ($his_gri_res as $g) {
                $his_filter_res_id_array[] = $g->res_id;
            }
        }
        $stat_gri_res = $this->getStatusResId();
        
        // faz a diferença dos arrays, para garantir que a reserva não tenha nenhum GRI que mude de status
        $filter_res_id_array = array_diff($his_filter_res_id_array, $stat_gri_res);
        $filteredArray = array_unique($filter_res_id_array);
        return $filteredArray;
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