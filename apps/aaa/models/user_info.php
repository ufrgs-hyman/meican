<?php

include_once 'libs/model.php';
include_once 'libs/acl_loader.php';
include_once 'apps/aaa/models/aros.php';
include_once 'apps/aaa/models/acos.php';

class user_info extends Model {
    var $displayField = "usr_name";

    function user_info() {
        $this->setTableName("user_info");

        // Add all table attributes
        $this->addAttribute("usr_id", "INTEGER", true, false, false);
        $this->addAttribute("usr_login", "VARCHAR", false, true, false);
        $this->addAttribute("usr_name", "VARCHAR");
        $this->addAttribute("usr_email", "VARCHAR");
        $this->addAttribute("usr_password", "VARCHAR", false, true, true, true);
        $this->addAttribute("usr_settings", "VARCHAR");
    }

    public function login() {
        $tableName = $this->getTableName();
        $sql = "SELECT * FROM $tableName WHERE usr_login='$this->usr_login' AND usr_password='$this->usr_password'";

        return $this->querySql($sql, $tableName);
    }

    public function getLogin() {
        $tableName = $this->getTableName();
        $sql = "SELECT usr_login FROM $tableName WHERE usr_id='$this->usr_id'";
        $result = $this->querySql($sql, $tableName);
        return $result[0]->usr_login;
    }
    
    /**
     * @param <type>
     * @param <boolean> $used = true retorna os grupos do usuário ; false retorna os grupos que o usuário não faz parte
     * @return DatabaseQuery
     */
    public function fetchGroups($used = TRUE) {
        if (!isset($this->usr_id))
            return 'Set $this->usr_id';

        $equal = ($used ? "NOT" : "");

        $sql = "SELECT g.* FROM group_info g
                LEFT JOIN user_group ug ON g.grp_id = ug.grp_id AND usr_id = $this->usr_id
                WHERE usr_id IS $equal NULL ";

        return $this->querySql($sql);
    }

    public function updateGroups($groups_array = array()) {

        // preenche um array somente com o Id dos grupos que o usuário faz parte
        $groups = $this->fetchGroups();
        $userGroups = array();
        if ($groups) {
            foreach ($groups as $g)
                $userGroups[] = $g->grp_id;
        }

        $toDelete = array_diff($userGroups, $groups_array);
        $toAdd = array_diff($groups_array, $userGroups);

        if (!$toDelete && !$toAdd)
            return FALSE;

        if ($toDelete) {
            // deleta grupos
            $strGrp = implode(',', $toDelete);
            $sql = "DELETE FROM user_group WHERE usr_id=$this->usr_id AND grp_id IN ($strGrp)";
            $resDel = $this->execSql($sql);

            if ($resDel) {
                foreach ($toDelete as $d) {
                    // remove da ARO
                    $parent_aro = new Aros($d, 'group_info');
                    $parents = $parent_aro->fetch(FALSE);
                    foreach ($parents as $p) {
                        $u_aro = new Aros($this->usr_id, 'user_info', $p->aro_id);
                        $result = $u_aro->fetch(FALSE);
                        //debug('removearo',$result[0]);
                        $result[0]->removeSubTree();
                    }

                    // remove da ACO
                    $parent_aco = new Acos($d, 'group_info');
                    $parents = $parent_aco->fetch(FALSE);
                    foreach ($parents as $p) {
                        $u_aco = new Acos($this->usr_id, 'user_info', $p->aco_id);
                        $result = $u_aco->fetch(FALSE);
                        //debug('removeaco',$result[0]);
                        $result[0]->removeSubTree();
                    }
                }
            }
        } else
            $resDel = TRUE;

        if ($toAdd) {
            // adiciona grupos
            $firstVal = TRUE;
            $values = "VALUES ";
            foreach ($toAdd as $grp_id) {
                if ($firstVal) {
                    $firstVal = FALSE;
                    $values .= "($this->usr_id, $grp_id)";
                } else
                    $values .= ", ($this->usr_id, $grp_id)";
            }

            $sql = "INSERT INTO user_group $values";
            $resAdd = $this->execSql($sql);

            if ($resAdd) {
                foreach ($toAdd as $grp_id) {

                    // insere na ARO
                    $parent_aro = new Aros($grp_id, 'group_info');
                    $usr_aro = new Aros($this->usr_id, 'user_info');

                    $new_user_aros = $parent_aro->addChild2($usr_aro);

                    // insere na ACO
                    $usr_aco = new Acos($this->usr_id, 'user_info');
                    $children = NULL;
                    if ($result = $usr_aco->fetch(FALSE)) {
                        $children = $result[0]->findChildren();
                    }

                    $parent_aco = new Acos($grp_id, 'group_info');
                    $new_user_acos = $parent_aco->addChild2($usr_aco);

                    if ($children) {
                        foreach ($new_user_acos as $user_aco) {
                            foreach ($children as $child) {
                                $user_aco->addChild2($child);
                            }
                        }
                    }

                    foreach ($new_user_aros as $aro_node) {
                        foreach ($new_user_acos as $aco_node) {
                            /** insere essa entrada na tabela AROS_ACOS
                             *  necessário para dar permissão ao usuário acessar tudo o que estiver abaixo dele
                             */
                            $new_aro = new Aros();
                            $new_aro->aro_id = $aro_node->parent_id;
                            $aro_parent = $new_aro->fetch(FALSE);
                            if (!$aro_parent)
                                continue;;

                            $new_aco = new Acos();
                            $new_aco->aco_id = $aco_node->parent_id;
                            $aco_parent = $new_aco->fetch(FALSE);
                            if (!$aco_parent)
                                continue;

                            if (($aro_parent[0]->obj_id == $aco_parent[0]->obj_id) && ($aro_parent[0]->model == $aco_parent[0]->model)) {
                                $aros_acos = new aros_acos();
                                $aros_acos->aro_id = $aro_node->aro_id;
                                $aros_acos->aco_id = $aco_node->aco_id;
                                $aros_acos->model = NULL;
                                $aros_acos->create = "allow";
                                $aros_acos->read = "allow";
                                $aros_acos->update = "allow";
                                $aros_acos->delete = "allow";
                                $aros_acos->insert();
                            }
                        }
                    }
                }
            }
        } else
            $resAdd = TRUE;

        return $resDel && $resAdd;
    }

    public function delete() {

        //checkacl
        if ($this->updateGroups())
            if (parent::delete(FALSE))
                return TRUE;

        return FALSE;
    }

    function insert($groups_array) {
        $new_user = parent::insert(); //info do user

        if ($new_user) {
            if ($new_user->updateGroups($groups_array)) {
                return $new_user;
            }
        }
        return FALSE;
    }

//    public function update($groups_array) {
//
//        if (parent::update())
//            if ($this->updateGroups($groups_array))
//                return TRUE;
//
//        return FALSE;
//    }

//            $aro_delete = new Aros($this->usr_id, 'user_info');
//            $aco_delete = new Acos($this->usr_id, 'user_info');
//
//             if (!($aco_delete->remove())) {
//              debug('falha para deletar aco do usr');
//                        return FALSE;
//
//             }
//
//
//                     if (!($aro_delete->remove())){
//                        debug('falha para deletar aro do usr');
//                        return FALSE;
//                     }
//
//            $child_aro = new Aros($this->usr_id, 'user_info');
////            $aro->model = $this->getTableName();
////            $aro->obj_id = $this->usr_id;
//
//            $child_aco = new Acos($this->usr_id, 'user_info');
////            $aco->model = $this->getTableName();
////            $aco->obj_id = $this->usr_id;
//
//
//
//            foreach ($groups_array as $g) {
//                //adicionar na arvore de aros: embaixo de cada grupo que o usuario faz parte
//                $parent_aro = new aros($g, 'group_info');
//                $resAddAro = $parent_aro->addChild2($child_aro);
//
//                if (!$resAddAro)
//                    break;
//
//                //arvore de acos, embaixo do container dos grupos
//                $parent_aco = new aros($g, 'container');
//                $resAddAco = $parent_aco->addChild2($child_aco);
//
//                if (!$resAddAco)
//                    break;
//            } //foreach
//            if ($resAddAco && $resAddAro)
//                return TRUE;
//        } else if ($resUpdate)//deu erro no updategroups ou nao foi alterado
//            return TRUE;
//        return FALSE;
    //funcao update
}

?>
