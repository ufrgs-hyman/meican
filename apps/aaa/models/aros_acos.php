<?php

include_once 'libs/Model/model.php';

class aros_acos extends Model {

    function aros_acos() {
        $this->setTableName("aros_acos");
        $this->addAttribute("perm_id", "INTEGER", true, false, false);
        $this->addAttribute("aro_id", "INTEGER");
        $this->addAttribute("aco_id", "INTEGER");
        $this->addAttribute("create", "VARCHAR");
        $this->addAttribute("read", "VARCHAR");
        $this->addAttribute("update", "VARCHAR");
        $this->addAttribute("delete", "VARCHAR");
        $this->addAttribute("model","VARCHAR");
    }

    public function insert() {
        if ($new_aro_aco = parent::insert()) {
            Common::apc_update();
            return $new_aro_aco;
        } else
            return FALSE;
    }

    public function delete($useACL = false) {
        if (parent::delete(FALSE)) {
            Common::apc_update();
            return TRUE;
        } else
            return FALSE;
    }

    public function update() {
        if (parent::update()) {
            Common::apc_update();
            return TRUE;
        } else
            return FALSE;
    }

}

?>
