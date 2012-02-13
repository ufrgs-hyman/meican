<?php

include_once 'libs/Model/tree_model.php';

class Aros extends Tree_Model {

     function Aros($obj_id="", $model="", $parent_id = "") {
        $this->setTableName("aros");
        $this->addAttribute("aro_id","INTEGER",true,false,false);
        $this->addAttribute("obj_id","INTEGER");
        $this->addAttribute("model","VARCHAR");
        $this->addAttribute("lft","INTEGER");
        $this->addAttribute("rgt","INTEGER");
        $this->addAttribute("parent_id","INTEGER");

        $this->obj_id = $obj_id;
        $this->model = $model;
        $this->parent_id = $parent_id;
    } 
}

?>
