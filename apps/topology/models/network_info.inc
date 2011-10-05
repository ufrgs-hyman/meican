<?php

include_once 'libs/resource_model.php';

class network_info extends Resource_Model {
    
    public function network_info() {

        $this->setTableName("network_info");

        // Add all table attributes
        $this->addAttribute("net_id","INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("net_descr","VARCHAR");
        $this->addAttribute("net_lat","FLOAT");
        $this->addAttribute("net_lng","FLOAT");
    }

//    public function fetchDevices() {
//        if (!isset($this->net_id)) {
//            //Framework::debug('Set $this->net_id');
//            return FALSE;
//        }
//
//        $sql = "SELECT di.* FROM device_info AS di
//                LEFT JOIN network_device AS nd ON di.dev_id = nd.dev_id AND nd.net_id = $this->net_id";
//
//        return $this->querySql($sql);
//    }

}

?>
