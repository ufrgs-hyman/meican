<?php

class Domain {
    private $id;
    private $topology_id;
    private $nodes = Array();
    
    public function setId($id){
        $this->id = $id;
    }
    
    public function setNode($node){
        $this->nodes[] = $node;
    }

    public function getDomainId(){
        $parts = explode(":", $this->id);
        $domain = $parts[3];
        $topologyId = str_replace("domain=", "", $domain);
        $this->topology_id = $topologyId;
    }

}

class Nodes {
     public $links = Array();
}

class Links {
    public $minVlan;
    public $maxVlan;
    public $granularity;
    public $capacity;
    public $minReservable;
    public $maxReservable;
}

class Urn {
    public $id;
    public $capacity;
    public $granularity;
    public $minimumReservable;
    public $maximumReservable;
    public $vlanRange;
}
?>
