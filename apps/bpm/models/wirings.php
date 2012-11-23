<?php

include_once 'apps/topology/models/meican_info.php';

include_once 'libs/Model/resource_model.php';
include_once 'libs/auth.php';

class wirings extends Resource_Model {

    function wirings() {
        $this->setTableName("wirings");

        // Add all table attributes
        $this->addAttribute('id', "INTEGER", true, false, false);
        $this->addAttribute("name", "VARCHAR");
        $this->addAttribute("working", "LONGTEXT");
        $this->addAttribute("language", "VARCHAR");
    }

    static public function listWirings($language) {
        $sql = "SELECT * FROM `wirings` WHERE `language`=" . $language;
        return parent::querySql($sql, 'wirings');
    }

}

?>
