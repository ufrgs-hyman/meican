<?php

include_once 'libs/Model/resource_model.php';

class urn_info extends Resource_Model {
    var $displayField = "urn_string";

    public function urn_info() {

        $this->setTableName("urn_info");

        // Add all table attributes
        $this->addAttribute("urn_id","INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("urn_string","VARCHAR", FALSE, TRUE, FALSE);
        $this->addAttribute("net_id","INTEGER");
        $this->addAttribute("dev_id","INTEGER");
        $this->addAttribute("port","VARCHAR", FALSE, TRUE, FALSE);
        $this->addAttribute("vlan","VARCHAR", FALSE, TRUE, FALSE);
        $this->addAttribute("max_capacity","INTEGER", FALSE, TRUE, FALSE);
        $this->addAttribute("min_capacity","INTEGER", FALSE, TRUE, FALSE);
        $this->addAttribute("granularity","INTEGER", FALSE, TRUE, FALSE);
    }
    
    public function update() {
        /**
         * @todo Corrigir: reservas embaixo do URN ficam órfãs
         */
        if (parent::update()) {
            $aco = new Acos($this->urn_id,"urn_info");
            if ($acos = $aco->fetch(FALSE)) {
                foreach ($acos as $a) {
                    $a->removeNode();
                }
            }
            
            $parent = new Acos($this->dev_id,"device_info");
            if ($parent->addChild2($aco)) {
                return TRUE;
            }
        }
        
        return FALSE;
    }
    
    public function verifyValidPort($dev_id, $urn_string) {
        if (!$dev_id)
            return false;
        
        $parts = explode(":", $urn_string);

        if (count($parts) > 5) {
            $port_attr = explode("=", $parts[5]);
            if (count($port_attr) == 2) {
                $this->dev_id = $dev_id;
                $this->port = null;
                if (strtoupper($port_attr[0]) == "PORT")
                    $this->port = $port_attr[1];

                if (!$this->port)
                    return false;

                if ($this->fetch())
                    return $this->port;
            }
        }
        return false;
    }

}

?>