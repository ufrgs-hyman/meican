<?php
include_once 'libs/model.inc';
include_once 'includes/auth.inc';

class request_info2 extends Model {

    function request_info() {

        $this->setTableName("request_info2");

        // Add all table attributes
        $this->addAttribute("req_id","INTEGER");
        $this->addAttribute("rsc_id","INTEGER");
        $this->addAttribute("rsc_type","VARCHAR");
    }
}

?>
