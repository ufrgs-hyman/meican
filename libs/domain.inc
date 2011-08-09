<?php

class Domain {
    public $nodes = Array();
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
?>
