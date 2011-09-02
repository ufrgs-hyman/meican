<?php

//include_once 'libs/resource_model.inc';
include_once 'libs/model.inc';

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