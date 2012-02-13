<?php

include_once 'libs/Model/model.php';
include_once 'apps/aaa/models/aros.php';
include_once 'apps/aaa/models/acos.php';

class group_info extends Model {
    var $displayField = "grp_descr";

    function group_info() {
        $this->setTableName("group_info");

        $this->addAttribute("grp_id", "INTEGER", true, false, false);
        $this->addAttribute("grp_descr", "VARCHAR");
    }

    public function fetchUsers($used = TRUE) {
        if (!isset($this->grp_id))
            return 'Set $this->grp_id';

        $equal = ($used ? "NOT" : "");

        $sql = "SELECT u.* FROM user_info u
                                LEFT JOIN user_group ug
                                    ON u.usr_id = ug.usr_id AND grp_id = $this->grp_id
                             WHERE grp_id IS $equal NULL";

        return $this->querySql($sql);
    }

    public function updateUsers($users_array = array()) {

        // preenche um array somente com o Id dos usuários que pertencem ao grupo
        $users = $this->fetchUsers();
        $users_of_group = array();

        if ($users) {
            foreach ($users as $u)
                $users_of_group[] = $u->usr_id;
        }

        if (!$users_array) {
            return TRUE;
        }

        $toDelete = array_diff($users_of_group, $users_array);
        $toAdd = array_diff($users_array, $users_of_group);

        if (!$toDelete && !$toAdd)
            return FALSE;

        if (count($toDelete) > 0) {
            $delArray = array();
            //procura se o usuario a ser deletado possui grupo. caso negativo, impede a exclusao
            foreach ($toDelete as $delId) {
                $user_tmp = new user_info();
                $user_tmp->usr_id = $delId;
                $groups_tmp = $user_tmp->fetchGroups();
                if (count($groups_tmp) > 1) {
                    $delArray[] = $delId;
                }
            }

            if ($delArray) {
                // deleta usuários
                $strUsr = implode(',', $delArray);
                $sql = "DELETE FROM user_group WHERE grp_id=$this->grp_id AND usr_id IN ($strUsr)";
                $resDel = $this->execSql($sql);
                if ($resDel) {
                    foreach ($delArray as $d) {
                        // remove da ARO
                        $parent_aro = new Aros($this->grp_id, 'group_info');
                        $parents = $parent_aro->fetch(FALSE);
                        foreach ($parents as $p) {
                            $u_aro = new Aros($d, 'user_info', $p->aro_id);
                            $result = $u_aro->fetch(FALSE);
                            //debug('removearo',$result[0]);
                            $result[0]->removeSubTree();
                        }

                        // remove da ACO
                        $parent_aco = new Acos($this->grp_id, 'group_info');
                        $parents = $parent_aco->fetch(FALSE);
                        foreach ($parents as $p) {
                            $u_aco = new Acos($d, 'user_info', $p->aco_id);
                            $result = $u_aco->fetch(FALSE);
                            //debug('removeaco',$result[0]);
                            $result[0]->removeSubTree();
                        }
                    }
                }
            } else
                $resDel = FALSE;
        } else
            $resDel = TRUE;

        if (count($toAdd) > 0) {
            // adiciona usuários
            $firstVal = TRUE;
            $values = "VALUES ";
            foreach ($toAdd as $usr_id) {
                if ($firstVal) {
                    $firstVal = FALSE;
                    $values .= "($usr_id, $this->grp_id)";
                } else
                    $values .= ", ($usr_id, $this->grp_id)";
            }

            $sql = "INSERT INTO user_group $values";
            $resAdd = $this->execSql($sql);

            if ($resAdd) {
                foreach ($toAdd as $usr_id) {

                    // insere na ARO
                    $parent_aro = new Aros($this->grp_id, 'group_info');
                    $usr_aro = new Aros($usr_id, 'user_info');

                    $new_user_aros = $parent_aro->addChild2($usr_aro);

                    // insere na ACO
                    $usr_aco = new Acos($usr_id, 'user_info');
                    $children = NULL;
                    if ($result = $usr_aco->fetch(FALSE)) {
                        $children = $result[0]->findChildren();
                    }

                    $parent_aco = new Acos($this->grp_id, 'group_info');
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

    public function insert($parents) {

        $new_group = parent::insert();

        $resAddAco = FALSE;
        $resAddAro = FALSE;

        if ($new_group) {
            $this->grp_id = $new_group->grp_id;
            $child = new stdClass(); //child é o nodo a ser adicionado
            $child->obj_id = $new_group->grp_id;
            $child->model = 'group_info';


            if (!$parents) { //nao tem nodo pai, será um nodo raiz
                $aro = new Aros();
                $aro->obj_id = $child->obj_id;
                $aro->model = $child->model;
                $resAddAro = $aro->addRootNode();

                //ACOS novo container
                $aco = new Acos();
                $aco->model = $child->model;
                $aco->obj_id = $child->obj_id;
                $resAddAco = $aco->addRootNode();
            } else {
                //para cada um dos pais do grupo $parents - grp_id
                foreach ($parents as $parent_id) {

                    //adiciona embaixo do nodo pai o nodo filho AROS
                    $parent_aro = new Aros($parent_id, 'group_info');
                    $resAddAro = $parent_aro->addChild2($child);

                    //adiciona embaixo do nodo pai o nodo filho ACOS
                    $parent_aco = new Acos($parent_id, 'group_info');
                    $resAddAco = $parent_aco->addChild2($child);
                }
            }
        }

        if ($resAddAco && $resAddAro)
            return TRUE;
        else
            return FALSE;
    }

    public function delete() {
        $tableName = $this->getTableName();
        if (parent::delete()) {

            //deleta só o nodo e passa seus filhos para o avô
            $aro = new Aros();
            $aro->model = 'group_info';
            $aro->obj_id = $this->grp_id;
            $arosToDel = $aro->fetch(FALSE);

            //$parent_aro = $aro->getParentNode();
            foreach ($arosToDel as $aro) {
                //atualiza arvore aro
                $resDelAro = $aro->removeNode();
            }
            //atualiza acos
            //remover group_info
            //remover container do grupo
            //passar os filhos para o nodo avô
            $aco = new Acos();
            $aco->model = 'group_info';
            $aco->obj_id = $this->grp_id;
            $acosToDel = $aco->fetch(FALSE);

            foreach ($acosToDel as $aco)
                $resDelAco = $aco->removeNode();

//        $users = $this->fetchUsers();
//        if ($users) {
//            //atualiza user_group
//            $usersToParent = array();
//            foreach ($users as $u) {
//                $usersToParent[] = $u->usr_id;
//            }
//            $parent_group = new group_info();
//            $parent_group->grp_id = $parent_aro->obj_id;
//            $resUpdate = $parent_group->updateUsers($usersToParent);
//        } else $resUpdate = TRUE;

            if ($resDelAco && $resDelAro) {
                debug("delete ok");

                $sql = "SELECT * FROM group_info";
                $res = $this->querySql($sql);
                //debug('teste',$res);
                return TRUE;
            } else
                return FALSE;
        } else
            return FALSE;
    }

}

?>