<?php

include_once 'libs/Model/resource_model.php';

class device_info extends Resource_Model {
    //var $displayField = "dev_descr";
    
    public function device_info() {

        $this->setTableName("device_info");

        // Add all table attributes
        $this->addAttribute("dev_id","INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("dev_descr","VARCHAR");
        $this->addAttribute("dev_ip","VARCHAR");
        $this->addAttribute("trademark","VARCHAR");
        $this->addAttribute("model","VARCHAR");
        $this->addAttribute("nr_ports","INTEGER");
        $this->addAttribute("net_id","INTEGER", FALSE, TRUE, FALSE);
        $this->addAttribute("node_id","VARCHAR");
    }
    
    public function fetchList() {
        if (parent::fetchList()) {
            $displayName = (empty($this->data[0]->dev_ip)) ? $this->data[0]->dev_descr : $this->data[0]->dev_descr.' - '.$this->data[0]->dev_ip;
            return $displayName;
        } else
            return false;
    }
    
    public function getDeviceFromNode($urn_string) {
        $parts = explode(":", $urn_string);

        if (count($parts) > 4) {
            $node_attr = explode("=", $parts[4]);
            $this->node_id = null;
            if (strtoupper($node_attr[0]) == "NODE")
                $this->node_id = $node_attr[1];

            if (!$this->node_id)
                return false;

            if ($result = $this->fetch(false))
                return $result[0];
        }
        return false;
    }

//    public function fetchNetwork() {
//        if (!isset($this->dev_id)) {
//            //debug('Set $this->dev_id');
//            return FALSE;
//        }
//
//        $sql = "SELECT ni.* FROM network_info AS ni
//                LEFT JOIN network_device AS nd ON ni.net_id = nd.net_id AND nd.dev_id = $this->dev_id";
//
//        $result = $this->querySql($sql);
//        if ($result)
//            return $result[0]->net_descr;
//    }

}

?>
