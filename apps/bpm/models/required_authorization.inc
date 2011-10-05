<?php

include_once 'libs/resource_model.php';

class required_authorization extends Resource_Model {

    function required_authorization() {

        $this->setTableName("required_authorization");

        // Add all table attributes
        $this->addAttribute("loc_id","INTEGER", true, false, false);
        $this->addAttribute("meican_ip","VARCHAR");
        $this->addAttribute("domain","VARCHAR");
        $this->addAttribute("req_id","INTEGER");
        $this->addAttribute("status","VARCHAR");
        $this->addAttribute("response","VARCHAR");
        $this->addAttribute("message","VARCHAR");
    }
}

?>
