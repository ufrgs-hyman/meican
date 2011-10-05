<?php

include_once 'libs/model.php';

class meican_info extends Model {
    
    function meican_info() {

        $this->setTableName("meican_info");

        // Add all table attributes
        $this->addAttribute("meican_id","INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("meican_descr","VARCHAR");
        $this->addAttribute("meican_ip","VARCHAR");
        $this->addAttribute("meican_dir_name","VARCHAR");
        $this->addAttribute("local_domain","INTEGER");
    }
    
    function getLocalMeicanId() {
        $this->local_domain = 1;
        $result = $this->fetch(FALSE);

        return $result[0]->meican_id;
    }

    function getLocalMeicanIp() {
        $this->local_domain = 1;
        $result = $this->fetch(FALSE);

        return $result[0]->meican_ip;
    }

     function getLocalMeicanDirName() {
        $this->local_domain = 1;
        $result = $this->fetch(FALSE);

        return $result[0]->meican_dir_name;
    }

}

?>
