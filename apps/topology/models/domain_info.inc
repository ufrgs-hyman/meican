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
        
        $topo_attr = explode("=", $parts[3]);
        $this->topology_id = NULL;
        if (strtoupper($topo_attr[0]) == "DOMAIN")
            $this->topology_id = $topo_attr[1];
        
        if (!$this->topology_id)
            return FALSE;

        if ($result = $this->fetch(FALSE))
            return $result[0];
        else
            return FALSE;
        //$this = $result[0];
    }

}

?>
