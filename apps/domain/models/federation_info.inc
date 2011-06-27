<?php

include_once 'libs/model.inc';

class federation_info extends Model {
    
    function federation_info() {

        $this->setTableName("federation_info");

        // Add all table attributes
        $this->addAttribute("fed_id","INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("fed_descr","VARCHAR");
        $this->addAttribute("fed_ip","VARCHAR");
    }

    function getLocalFederation() {
        $this->fed_ip = Framework::$fedIp;
        $result = $this->fetch(FALSE);

        return $result[0];
    }

}

?>
