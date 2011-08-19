<?php

include_once 'libs/model.inc';

class remote_authorization_info extends Model {

    function remote_authorization_info() {

        $this->setTableName("remote_authorization_info");

        // Add all table attributes
        $this->addAttribute("meican_ip","VARCHAR");
        $this->addAttribute("domain","VARCHAR");
        $this->addAttribute("req_id","INTEGER");
        $this->addAttribute("status","VARCHAR");
        $this->addAttribute("response","VARCHAR");
        $this->addAttribute("message","VARCHAR");
    }
}

?>
