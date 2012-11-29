<?php

include_once 'apps/topology/models/meican_info.php';

include_once 'libs/Model/model.php';
include_once 'libs/auth.php';

class workflows_info extends Model {

    function workflows_info() {
        $this->setTableName("workflows_info");

        // Add all table attributes
        $this->addAttribute('id', "INTEGER", true, false, false);
        $this->addAttribute("name", "VARCHAR");
        $this->addAttribute("working", "VARCHAR");
        $this->addAttribute("language", "VARCHAR");
        $this->addAttribute("dom_id", "INTEGER");
        $this->addAttribute("status", "INTEGER");
    }
    

}

?>
