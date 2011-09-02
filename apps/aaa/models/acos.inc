<?php

include_once 'libs/tree_model.inc';

class Acos extends Tree_Model {

     function Acos($obj_id="", $model="",$parent_id="") {
        $this->setTableName("acos");
        $this->addAttribute("aco_id","INTEGER",true,false,false);
        $this->addAttribute("obj_id","INTEGER");
        $this->addAttribute("model","VARCHAR");
        $this->addAttribute("lft","INTEGER");
        $this->addAttribute("rgt","INTEGER");
        $this->addAttribute("parent_id","INTEGER");

        $this->obj_id = $obj_id;
        $this->model = $model;
        $this->parent_id = $parent_id;
    }


    public function getAcoGroupByModel($strAcos, $model) {

        $sql = "SELECT DISTINCT obj_id FROM acos WHERE aco_id IN ($strAcos) AND model='$model' AND obj_id IS NOT NULL";
        Framework::debug("aco grp",$sql);
        $result = $this->querySql($sql, $this->getTableName());
        return $result;
    }

    public function getVoid($strAcos, $model) {

        $sql = "SELECT DISTINCT * FROM acos WHERE aco_id IN ($strAcos) AND model='$model' AND obj_id IS NULL";
        $result = $this->querySql($sql, $this->getTableName());
        return $result;
    }
}

?>
