<?php

include_once 'libs/resource_model.inc';

class domain_info extends Resource_Model {

    function domain_info() {

        $this->setTableName("domain_info");

        // Add all table attributes
        $this->addAttribute("dom_id","INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("dom_descr","VARCHAR");
        $this->addAttribute("oscars_ip","VARCHAR");
        $this->addAttribute("topology_id","VARCHAR");
        $this->addAttribute("ode_ip","VARCHAR");
        $this->addAttribute("ode_wsdl_path","VARCHAR");
    }

    public function getDom(){
        $tmp = $this->fetch();
        return $tmp[0];
    }

    public function getOSCARSDomain($urn_string) {
        $parts = explode(":", $urn_string);
        $domain = $parts[3];
        $topologyId = str_replace("domain=", "", $domain);
        $this->topology_id = $topologyId;

        if ($result = $this->fetch(FALSE))
            return $result[0];
        else
            return FALSE;
        //$this = $result[0];
    }

}

?>
