<?php

include_once 'libs/model.inc';

class Tree_Model extends Model {

    function Tree_Model() {

    }

    function addChild2($child) {

        if (!($child->obj_id && $child->model)) {
            Framework::debug('child param not set', $child);
            return FALSE;
        }

        $tableName = $this->getTableName();

        $pk = $this->getPrimaryKey();

        $sqlQuery = "";
        if ($this->{$pk}) {
            $whereString = $this->buildWhere(array($pk));
            $sqlQuery = "SELECT $pk,rgt FROM $tableName WHERE $whereString";
        } elseif (($this->obj_id || $this->obj_id === NULL) && $this->model) {
            $whereString = $this->buildWhere(array('obj_id','model'));
            $sqlQuery = "SELECT $pk,rgt FROM $tableName WHERE $whereString";
        } else {
            Framework::debug('parent node param not set', $this);
            return FALSE;
        }

        $result = $this->fetchSql($sqlQuery);

        $sqlQuery = "";
        foreach ($result as $r) {
            //testa para ver se não vai ser adicionado duplicado : abaixo de determinado pai (parent_id), o conjunto de obj_id e model torna-se único
            $testeFetch = "SELECT * FROM $tableName WHERE obj_id=$child->obj_id AND model='$child->model' AND parent_id=".$r->{$pk};
            if (!($this->fetchSql($testeFetch)))
                $sqlQuery .= "UPDATE $tableName SET rgt = rgt + 2 WHERE rgt >= $r->rgt;
                         UPDATE $tableName SET lft = lft + 2 WHERE lft > $r->rgt;
                         INSERT INTO $tableName(obj_id, model, lft, rgt, parent_id) VALUES($child->obj_id, '$child->model',  $r->rgt,  $r->rgt + 1,".$r->{$pk}.");";
        }

        if ($this->transactionSql($sqlQuery)) {
            Common::apc_update();
            
            $parents = $this->fetch(FALSE);
            $parent_array = array();
            foreach ($parents as $p) {
                $parent_array[] = $p->{$pk};
            }
            
            $parent_str = implode(",", $parent_array);

            //verificar se é utilizado
            $sql = "SELECT * from $tableName WHERE obj_id=$child->obj_id AND model='$child->model' AND parent_id in ($parent_str)";
            return $this->fetchSql($sql, $tableName);
            
        } else return FALSE;
    }

    //deleta nodo e os filhos
    public function removeSubTree() {

        $tableName = $this->getTableName();

        $pk = $this->getPrimaryKey();

        if (!$this->{$pk}) {
            Framework::debug('set the primary key to removesubtree.',$pk);
            return FALSE;
        }

        $sqlQuery = "SELECT lft, rgt, (rgt - lft + 1) as width FROM $tableName WHERE $pk=".$this->{$pk};
        $result = $this->fetchSql($sqlQuery);

        $sqlQuery = "LOCK TABLE $tableName WRITE;";
        foreach ($result as $res) {
            $right = $res->rgt;
            $left = $res->lft;
            $width = $res->width;

            $sqlQuery .= "DELETE FROM $tableName WHERE lft BETWEEN $left AND $right;
                     UPDATE $tableName SET rgt = rgt - $width WHERE rgt > $right;
                     UPDATE $tableName SET lft = lft - $width WHERE lft > $right;";
        }

         $sqlQuery .= "UNLOCK TABLES;";

        if ($this->transactionSql($sqlQuery)){
                 Common::apc_update();
                 return TRUE;
        }
        return FALSE;
    }

    public function removeNode() {

        $tableName = $this->getTableName();
        $pk = $this->getPrimaryKey();

        if (!$this->{$pk}) {
            Framework::debug('set the primary key to removenode.',$pk);
            return FALSE;
        }

        $sqlQuery = "SELECT lft, rgt FROM $tableName WHERE $pk=".$this->{$pk};
        $result = $this->fetchSql($sqlQuery);

        $res = $result[0];
        $right = $res->rgt;
        $left = $res->lft;


        $sqlQuery = "DELETE FROM $tableName WHERE $pk=".$this->{$pk}.";"; //deleta o nodo

        //modifica os nodos filhos do nodo que foi deletado, passando os filhos para o pai do nodo deletado
        $sqlQuery .= "UPDATE $tableName SET lft = lft - 1 WHERE lft BETWEEN $left AND $right;";
        $sqlQuery .= "UPDATE $tableName SET rgt = rgt - 1 WHERE rgt BETWEEN $left AND $right;";
        //modifica nodos vizinhos a direita do nodo deletado
        $sqlQuery .= "UPDATE $tableName SET lft = lft - 2 WHERE lft > $right;";
        $sqlQuery .= "UPDATE $tableName SET rgt = rgt - 2 WHERE rgt > $right;";

        if ($this->transactionSql($sqlQuery)){
            Framework::debug("remove node");
            Common::apc_update();
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function addRootNode() {
        $tableName = $this->getTableName();
        $sql = "SELECT MAX(rgt) as rgt FROM $tableName";

        $result = $this->fetchSql($sql);
        if ($result) {


            $max = $result[0]->rgt;

            $left = $max + 1;
            $right = $max + 2;

            $sql = "INSERT INTO $tableName(obj_id, model, lft, rgt, parent_id) VALUES($this->obj_id, '$this->model',  $left,  $right, NULL)";

            if ($this->querySql($sql)){
                Common::apc_update();
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getImmediateNodes() {
        $tableName = $this->getTableName();

        $pk = $this->getPrimaryKey();

        if ($this->model)
            $sql = "SELECT * FROM $tableName WHERE parent_id=".$this->{$pk}." and model='$this->model'";
        else $sql = "SELECT * FROM $tableName WHERE parent_id=".$this->{$pk};
       
        return $this->fetchSql($sql, $tableName);

    }


    function getRootNodes(){
         $tableName = $this->getTableName();

         $sql = "SELECT * FROM $tableName WHERE parent_id IS NULL";

         return $this->fetchSql($sql, $tableName);
    }

    //busca filhos e também o próprio nodo
    function findChildren($options=NULL) {
        $tableName = $this->getTableName();

        unset($and);
        $pk = $this->getPrimaryKey();

        if (!$this->{$pk}) {
            Framework::debug('set the primary key to findchildren.',$pk);
            return FALSE;
        } else {
            $and = " AND parent.$pk=".$this->{$pk};
        }


        $sqlQuery = "SELECT DISTINCT node.*
                    FROM $tableName AS node,
                $tableName AS parent
                    WHERE (node.lft BETWEEN (parent.lft +1) AND (parent.rgt-1)) $and";


        return $this->fetchSql($sqlQuery, $tableName);
    }
    
    public function getParentNodes() {
        $pk = $this->getPrimaryKey();
        $tablename = $this->getTableName();

        $tree_node = new $tablename;
        $tree_node->obj_id = $this->obj_id;
        $tree_node->model = $this->model;

        $parents = array();

        if (isset($this->{$pk})) {
            $tree_node->$pk = $this->{$pk};
        }

        if ($nodes = $tree_node->fetch(FALSE)) {
            foreach ($nodes as $n) {
                if ($n->parent_id) {
                    $parent_node = new $tablename;
                    $parent_node->$pk = $n->parent_id;

                    if ($parent = $parent_node->fetch(FALSE)) {
                        $parents[] = $parent[0];
                    }
                }
            }
        }

        return $parents;
    }

} //da classe

/**
 * DECRAPTED FUNCTIONS
 */


//    //adiciona objeto embaixo do pai selectionado
//
//    function addChild($parent_pk) { // refazer
//        /**
//         * @todo transformar $parent em array para deixar análogo ao remove. Colocar o laço dentro dessa função
//         */
//
//        $tableName = $this->getTableName();
//
//        $sqlQuery = "LOCK TABLE $tableName WRITE";
//        $this->querySql($sqlQuery);
//
//        $pk = $this->getPrimaryKey();
//
//        $sqlQuery = "SELECT $pk,rgt FROM $tableName WHERE $pk=$parent_pk";
//        $result = $this->fetchSql($sqlQuery);
//
//
//        unset($sqlQuery);
//        foreach ($result as $r) {
//            $sqlQuery .= "UPDATE $tableName SET rgt = rgt + 2 WHERE rgt >= $r->rgt;
//                         UPDATE $tableName SET lft = lft + 2 WHERE lft > $r->rgt;
//                         INSERT INTO $tableName(obj_id, model, lft, rgt, parent_id) VALUES($this->obj_id, '$this->model',  $r->rgt,  $r->rgt + 1,".$r->{$pk}.");";
//        }
//
//
//        $result = $this->transactionSql($sqlQuery);
//
//        //Framework::debug('insert',$sqlQuery);
//
//        $sqlQuery = "UNLOCK TABLES";
//
//        if ($this->querySql($sqlQuery))
//            return $this->fetch(FALSE);
//
//        else return FALSE;
//    }


//    function getParentNode(){
//        $tableName = $this->getTableName();
//        $pk = $this->getPrimaryKey();
//
//        unset($and);
//
//
//            if (!$this->{$pk}){
//                 Framework::debug('set the primary key to getparentnode.',$pk);
//                return FALSE;
//            } else {
//                    $and = "AND node.$pk =".$this->{$pk};
//            }
//
//        $sql = "SELECT DISTINCT parent.* FROM $tableName AS node, $tableName AS parent
//                WHERE node.lft > parent.lft AND node.lft < parent.rgt
//                AND ";
//
//        $sql .= $and;
//
//        $sql .= "AND parent.model='group_info'
//                ORDER BY parent.lft DESC
//                LIMIT 0,1";
//
//        //Framework::debug('parentnode',$sql);
//        //primeiro resultado será o pai direto
//        return $this->querySql($sql);
//
//    }

//    /**
//     *
//     * @return <type> busca grupos pais de determinado nodo setado
//     */
//    function getParentNodes(){
//        $tableName = $this->getTableName();
//        //parents
//        $sql = "SELECT DISTINCT parent.* FROM $tableName AS node, $tableName AS parent
//                WHERE node.lft BETWEEN parent.lft AND parent.rgt
//                AND node.obj_id = $this->obj_id
//                AND node.model = '$this->model'
//                AND parent.model='group_info'
//                ORDER BY parent.lft DESC";
//
//        $tmp = $this->fetchSql($sql, $tableName);
//        return $tmp;
//    }



?>
