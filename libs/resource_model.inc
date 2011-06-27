<?php

include_once 'includes/auth.inc';
include_once 'apps/aaa/models/acos.inc';
include_once 'libs/model.inc';

class Resource_Model extends Model {

    public function insert($parent_id = NULL, $model = NULL) {
        $inserted_obj = parent::insert();
        
        if ($inserted_obj) {
            //arvore de acos, nodo vai para embaixo do usuário
            if (!$parent_id) {
                $p_id = AuthSystem::getUserId();
                $p_aco = new Acos($p_id, 'user_info');
            } else
                $p_aco = new Acos($parent_id, $model);

            $obj = $inserted_obj;
            $pk = $obj->getPrimaryKey();
            $c_model = $obj->getTableName();
            $child_aco = new Acos($obj->{$pk}, $c_model);

            if ($p_aco->addChild2($child_aco))
                return $obj;
            else
                $obj->delete();
        }

        return FALSE;
    }

    public function delete() {
        $model = $this->getTableName();
        $pk = $this->getPrimaryKey();
        $aco = new Acos($this->{$pk}, $model);
        $result = $aco->fetch(FALSE);

        if (parent::delete()) {
            foreach ($result as $r)
                $acoRes = $r->removeNode();

            if ($acoRes)
                return TRUE;
        }

        return FALSE;
    }

}

?>
