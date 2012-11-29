<?php

include_once 'apps/topology/models/meican_info.php';

include_once 'libs/Model/resource_model.php';
include_once 'libs/auth.php';

class workflows_info extends Resource_Model {

    function workflows_info() {
        $this->setTableName("workflows_info");

        // Add all table attributes
        $this->addAttribute('id', "INTEGER", true, false, false);
        $this->addAttribute("name", "VARCHAR");
        $this->addAttribute("working", "LONGTEXT");
        $this->addAttribute("language", "VARCHAR");
    }
    

}

?>
