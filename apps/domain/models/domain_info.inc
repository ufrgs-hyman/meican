<?php

include_once 'libs/resource_model.inc';

class domain_info extends Resource_Model {
    
    function domain_info() {

        $this->setTableName("domain_info");

        // Add all table attributes
        $this->addAttribute("dom_id","INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("dom_descr","VARCHAR");
        $this->addAttribute("oscars_ip","VARCHAR");
        $this->addAttribute("topo_domain_id","VARCHAR");
    }

}

?>
