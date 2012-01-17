<?php

include_once 'libs/model.php';
include_once 'libs/auth.php';

class authorization_info extends Model {

    function authorization_info() {

        $this->setTableName("authorization_info");

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
