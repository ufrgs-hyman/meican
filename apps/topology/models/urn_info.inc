<?php

include_once 'libs/resource_model.php';

class urn_info extends Resource_Model {
    var $displayField = "urn_string";

    public function urn_info() {

        $this->setTableName("urn_info");

        // Add all table attributes
        $this->addAttribute("urn_id","INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("urn_string","VARCHAR");
        $this->addAttribute("net_id","INTEGER");
        $this->addAttribute("dev_id","INTEGER");
        $this->addAttribute("port","VARCHAR");
        $this->addAttribute("vlan","VARCHAR");
        $this->addAttribute("max_capacity","INTEGER");
        $this->addAttribute("min_capacity","INTEGER");
        $this->addAttribute("granularity","INTEGER");
    }
    
    public function update() {
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

}

?>