<?php

include_once 'libs/auth.php';
include_once 'apps/aaa/models/acos.php';
include_once 'libs/model.php';

class Resource_Model extends Model {

    public function insert($parent_id = NULL, $model = NULL) {
        $inserted_obj = parent::insert();

        if ($inserted_obj) {
            if (($parent_id === NULL) && ($model === NULL)) {
                //arvore de acos, nodo vai para embaixo do usuário
                $p_id = AuthSystem::getUserId();
                $p_aco = new Acos($p_id, 'user_info');
            } elseif ($model)
            //arvore de acos, nodo vai embaixo do objeto (ou void) e modelo especificado
                $p_aco = new Acos($parent_id, $model);
            else {
                Framework::debug("not enough arguments in tree model insert");
                return FALSE;
            }

            if (is_a($inserted_obj, "Model")) {

                $obj = $inserted_obj;
                $pk = $obj->getPrimaryKey();
                $c_model = $obj->getTableName();
                $child_aco = new Acos($obj->{$pk}, $c_model);

                if ($p_aco->addChild2($child_aco))
                    return $obj;
                else
                    $obj->delete();
            }
        }

        return FALSE;
    }

    public function delete() {
        $model = $this->getTableName();
        $pk = $this->getPrimaryKey();

        if (!$this->{$pk} && !$model)
            return FALSE;

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
