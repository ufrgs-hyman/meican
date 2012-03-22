<?php
include_once 'libs/Model/attribute.php';
//include_once 'libs/acl_loader.php';
App::uses('ConnectionManager', 'Model');

class Model extends Object {

    public $attributes;
    private $tableName;
    private $databaseString;

    function Model() {
        $this->attributes = array();
    }

    public function getDatabaseString() {
        return $this->databaseString;
    }

    public function setDatabaseString($string) {
        $this->databaseString = $string;
        return true;
    }

    public function setAccessTableName($tableName) {
        $this->accessTableName = $tableName;
        return true;
    }

    protected function addAttribute($name, $type, $primaryKey=false, $usedInInsert=true, $usedInUpdate=true, $forceUpdate=false) {
        $this->attributes[$name] = new Attribute($name, $type, $primaryKey, $usedInInsert, $usedInUpdate, $forceUpdate);
        $this->$name = "";
    }

    public function setTableName($n) {
        $this->tableName = $n;
    }

    public function getTableName() {
        if ($this->tableName == "") {
            return get_class($this);
        }
        return $this->tableName;
    }

    public function getPrimaryKey() {
        $primaryKey = NULL;

        foreach ($this->attributes as $attribute) {
            if ($attribute->primaryKey) {
                $primaryKey = $attribute->name;
                break;
            }
        }
        return $primaryKey;
    }

    public function getValidInds() {
        return (Common::arrayExtractAttr($this->attributes, "name"));
    }

    /**
     *
     * @param <type> $options
     * @return <boolean> FALSE: no object found
     * @return <array> Object Model: objects were found
     */
    public function fetch($useACL = true) {
        $tableName = $this->getTableName();
        $whereArgsString = $this->buildWhere();

        $sql = "";
        if ($useACL) {
            include_once 'libs/acl_loader.php';
            $acl = AclLoader::getInstance();
            $allowPks = $acl->getAllowedPKey('read', $tableName);

            if ($allowPks) {
                $inString = implode(', ', $allowPks);
                $pk = $this->getPrimaryKey();
                if ($whereArgsString)
                    $sql = "SELECT * FROM `$tableName` WHERE $whereArgsString AND `$pk` IN ($inString)";
                else
                    $sql = "SELECT * FROM `$tableName` WHERE `$pk` IN ($inString)";
            } else {
                $empty = array();
                return $empty; //sem acesso a nada
            }
        } else { //sem ACL
            if ($whereArgsString)
                $sql = "SELECT * FROM `$tableName` WHERE $whereArgsString";
            else
                $sql = "SELECT * FROM `$tableName`";
        }
        //debug("fetch",$sql);
        return ($this->data = $this->querySql($sql, $tableName));
    }

    /**
     * @example Before calling this function, all the 'usedInUpdate' attributes must be set, even when it should be NULL.
     * Otherwise, the param will be set blank
     * @return Boolean TRUE if update was success, FALSE otherwise
     */
    public function update() {
        $pk = $this->getPrimaryKey();
        if (!$this->{$pk})
            return FALSE;

        $classname = $this->getTableName();
        $values = get_object_vars($this);
        $sql = "UPDATE `$classname` SET ";
        $isFirst = true;

        foreach ($this->attributes as $attribute) {
            $name = $attribute->name;
            if (($attribute->type == "VARCHAR") && ($values[$name] !== NULL)) {
                $values[$name] = "'" . $values[$name] . "'";
            }
            if ($attribute->usedInUpdate) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $sql.=", ";
                }
                if ($values[$name] === NULL)
                    $sql .= "`$name`=NULL";
                else
                    $sql .= "`$name`=" . $values[$name];
            }
        }

        $where = " WHERE `$pk`=" . $this->{$pk};

        $sql .= $where;
        //debug('update',$sql);
        return $this->execSql($sql);
    }

    /**
     * @deprecated OBSOLET FUNCTION -> replaced by update()
     * 
     */
    /**
     * Utiliza a PRIMARY_KEY do modelo para selecionar a linha a ser alterada.
     * Esse atributo precisa estar setado.
     * Os demais atributos que estiverem setados serão alterados. Os atributos que não estiverem setados
     * serão mantidos.
     *
     * @return <boolean> TRUE if update was success, FALSE otherwise
     */
    /*
      public function update2() {

      //algoritmo simplificado:
      //achar a chave primária
      //fazer um fetch com o valor da chave primária setado no controller
      //alterar SOMENTE os atributos que foram setados no controller e deixar os demais inalterados
      // inicia objeto temporário para manter as informações do objeto original


      $validInd = array();

      // varre a estrutura em busca da chave primária e constrói um vetor com índices válidos
      foreach ($this->attributes as $attribute) {
      if ($attribute->primaryKey)
      $primaryKey[] = $attribute->name;

      $validInd[] = $attribute->name;
      }

      // copia a chave primária (será usada na busca da linha a ser alterada no banco)
      //return "ERROR: database_object.inc -> UPDATE : PRIMARY KEY $primaryKey NÃO SETADA.";
      $fetchObj = new $this->tableName;
      foreach ($primaryKey as $pk) {
      if (!$this->$pk) {
      debug('set all the primary keys to update.', $pk);
      return FALSE;
      }
      else
      $fetchObj->$pk = $this->$pk;
      }

      $result = $fetchObj->fetch(FALSE);
      $fetchResult = $result[0];

      $changed = FALSE;
      if ($fetchResult !== FALSE) {
      // varre os atributos do objeto original, verifica se não está setado
      foreach ($this as $name => $val) {
      if (array_search($name, $validInd) !== FALSE) { //verifica se o índice selecionado do objeto é um índice válido
      if ($val) { //se atributo do modelo estiver setado
      if (($val != $fetchResult->$name) || ($this->attributes[$name]->forceUpdate)) //testa se o valor é diferente OU é um atributo que SEMPRE deve ser alterado
      $changed = TRUE;
      } else {
      // copia o resultado da busca feita sobre o objeto temporário para o objeto original (os valores que não serão alterados)
      $this->$name = $fetchResult->$name;
      }
      }
      }
      } else
      return FALSE;
      /** @todo : log
      //return "ERROR: database_object.inc -> UPDATE : PRIMARY KEY $primaryKey NÃO ENCONTRADA.";
     *
     *
      if (!$changed) {
      debug("not updated");
      return FALSE;
      }

      if (sizeof($this->attributes) == 0)
      //return "Atributos invalidos";
      return FALSE;

      $classname = $this->getTableName();
      $values = get_object_vars($this);
      $sql = "UPDATE `$classname` SET ";
      $isFirst = true;
      $isFirstWhere = true;
      $where = " WHERE ";
      foreach ($this->attributes as $attribute) {
      $name = $attribute->name;
      if (($attribute->type == "VARCHAR") && ($values[$name] !== NULL)) {
      $values[$name] = "'" . $values[$name] . "'";
      }
      if ($attribute->usedInUpdate) {
      if ($isFirst) {
      $isFirst = false;
      } else {
      $sql.=", ";
      }
      if ($values[$name] === NULL)
      $sql .= "`$name`=NULL";
      else
      $sql .= "`$name`=" . $values[$name];
      }
      if ($attribute->primaryKey) {
      if ($isFirstWhere) {
      $isFirstWhere = false;
      } else {
      $where .= " AND ";
      }
      $where .= "`$name`=" . $values[$name];
      }
      }
      $sql .= $where;
      //debug('update',$sql);
      return $this->execSql($sql);
      }
     */

    /**
     * 
     * @param Array $alt An array containing only the attributes to update
     * @param Boolean $useACL Whether to use ACL or not, default is TRUE
     * @return Boolean TRUE if operation was successful, FALSE otherwise
     * @example This function is better to use when only some attributes of the model are updated, and not all of them
     */
    public function updateTo($alt = Array(), $useACL = TRUE) {

        if (!$alt)
            return FALSE;


        $tableName = $this->getTableName();
        $values = get_object_vars($this);

        $validInds = $this->getValidInds();
        $setArgs = array();
        foreach ($alt as $ind => $val) {
            if ($val && array_search($ind, $validInds) !== FALSE) { //indice valido
                if ($this->attributes[$ind]->type == "VARCHAR")
                    $alt[$ind] = "\"" . $alt[$ind] . "\"";
                $setArgs[] = "$ind=$alt[$ind]";
            }
        }

        if (isset($setArgs))
            $setArgsString = implode(',', $setArgs);
        else
            return FALSE;

        if (!$setArgsString)
            return FALSE;

        $whereArgsString = $this->buildWhere();
        $sql = "";
        $sqlfetch = "";

        if ($useACL) {

            include_once 'libs/acl_loader.php';
            $acl = AclLoader::getInstance();
            $allowPks = $acl->getAllowedPKey('update', $tableName);

            if ($allowPks) {
                $inString = implode(',', $allowPks);
                $pk = $this->getPrimaryKey();
                if ($whereArgsString) {
                    $sql = "UPDATE $tableName SET $setArgsString WHERE $whereArgsString AND $pk IN ($inString)";
                    $sqlfetch = "SELECT * FROM $tableName WHERE $whereArgsString AND $pk IN ($inString)";
                } else {
                    $sql = "UPDATE $tableName WHERE $pk IN ($inString)";
                    $sqlfetch = "SELECT * FROM $tableName WHERE $pk IN ($inString)";
                }
            } else
                return FALSE; //sem acesso a nada
        } else { //sem ACL
            if ($whereArgsString) {
                $sql = "UPDATE $tableName SET $setArgsString WHERE $whereArgsString";
                $sqlfetch = "SELECT * FROM $tableName WHERE $whereArgsString";
            }
            else
                $sql = "UPDATE $tableName SET $setArgsString";
        }

        $resfetch = $this->querySql($sqlfetch, $tableName);

        //debug('sql update', $sql);

        if (!$resfetch)
            return FALSE;
        else
            return $this->execSql($sql);
    }

//do updateTo

    /**
     * @return Boolean FALSE : on failed insertion (FAILED)
     * @return Class Object : the inserted object if it was possible to find it (SUCCESS)
     * @return Boolean TRUE : the object was inserted, but it was not possible to find it (SUCCESS)
     */
    public function insert() {

        $classname = $this->getTableName();
        $values = get_object_vars($this);
        $isFirst = true;
        $sqlNames = "";
        $sqlValues = "";
        foreach ($this->attributes as $attribute) {
            if ($attribute->usedInInsert) {
                $name = $attribute->name;

                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $sqlValues.=", ";
                    $sqlNames.=", ";
                }

                $sqlNames .= "`$name`";

                if ($values[$name] === NULL)
                    $sqlValues .= "NULL";
                else {
                    if ($attribute->type == "VARCHAR")
                        $sqlValues .= "'" . $values[$name] . "'";
                    else
                        $sqlValues .= $values[$name];
                }
            }
        }
        $sql = "INSERT INTO `$classname` ($sqlNames) values ($sqlValues)";

        //Log::write('debug', "SQL insert:\n" . print_r($sql, true));

        $id = $this->insertSql($sql);
        if ($id !== FALSE) {
            // se ID não for idêntico a FALSE, é porque foi inserido
            // agora TENTA buscar objeto inserido para retornar
            $pk = $this->getPrimaryKey();
            if ($id && $pk) {
                // se retornou um ID válido, busca por ele como PK
                $object = new $classname;
                $object->$pk = $id;
                if ($ret = $object->fetch(FALSE))
                    return $ret[0];
            } else {
                // se não retornou ID válido, busca pelos atributos utilizados na inserção
                if ($ret = $this->fetch(FALSE))
                    return $ret[0];
            }
            // se não conseguiu buscar objeto, apenas retorna TRUE
            Log::write('warning', "Object inserted but not found");
            return TRUE;
        } else {
            // objeto não inserido, retorna FALSE
            Log::write('error', "Error to execute insert SQL:\n" . print_r($sql, TRUE));
            return FALSE;
        }
    }

    /**
     *
     * @return Boolean TRUE if delete was success, FALSE otherwise
     */
    public function delete($useACL = TRUE) {
        $tableName = $this->getTableName();
        $pk = $this->getPrimaryKey();

        $fetch = $this->fetch(FALSE);

        if (!$fetch)
            return FALSE;

        $toDelete = array();
        foreach ($fetch as $f) { //retorna todas as chaves primarias para deletar
            $toDelete[] = $f->{$pk};
        }

        //debug('todelete', $toDelete);
        if (!$toDelete) {
            return FALSE;
        }

        $values = get_object_vars($this);
        $whereArgs = array();
        if ($useACL) {
            include_once 'libs/acl_loader.php';
            $acl = AclLoader::getInstance();
            $restr = $acl->getAllowedPKey('delete', $tableName);

            //delete nao permite where do tipo IN
            foreach ($toDelete as $d) {
                if (array_search($d, $restr) !== FALSE) { //verifica se a chave q quer deletar está dentro das permissoes
                    if ($this->attributes[$pk]->type == "VARCHAR")
                        $whereArgs[] = "`$pk`='$d'";
                    else
                        $whereArgs[] = "`$pk`=$d";
                }
            }
        } else {
            foreach ($toDelete as $d) {
                if ($this->attributes[$pk]->type == "VARCHAR")
                    $whereArgs[] = "`$pk`='$d'";
                else
                    $whereArgs[] = "`$pk`=$d";
            }
        }


        $sql = NULL;
        if ($whereArgs) {
            $whereArgsString = implode(' OR ', $whereArgs);
            $sql = "DELETE FROM `$tableName` WHERE $whereArgsString";

            //fetch before update to return false if none results will be selected
            $sqlfetch = "SELECT * FROM $tableName WHERE $whereArgsString";

            $result = $this->querySql($sqlfetch, $tableName);

            if (!$result) //nao consigurira atualizar nada
                return FALSE;
        }

        return $this->execSql($sql);
    }

    /**
     *
     * @param String $sql : well-formatted SQL string
     * @return Boolean Object ID if insert was successful, FALSE otherwise
     */
    protected function insertSql($sql) {
        $ds = ConnectionManager::getDataSource('default');
        if (!($ds && $sql))
            return FALSE;

        if ($ds->execute($sql))
            return $ds->lastInsertId();
        else
            return false;
    }

    /**
     *
     * @param String $sql well-formatted SQL string
     * @return Boolean TRUE if exec was successful, FALSE otherwise
     */
    protected function execSql($sql) {
        $ds = ConnectionManager::getDataSource('default');
        if (!($ds && $sql))
            return FALSE;

        return $ds->execute($sql);
    }

    /**
     *
     * @param <string> $sql : SQL string using ';' as separator for the 'explode()' function.
     *                  Ex:  "DELETE FROM $tableName WHERE lft BETWEEN $left AND $right;
     *                        UPDATE $tableName SET rgt = rgt - $width WHERE rgt > $right;
     *                        UPDATE $tableName SET lft = lft - $width WHERE lft > $right;"
     *
     * @return <boolean> TRUE if transaction was successful. FALSE otherwise.
     */
    protected function transactionSql($sql) {

        $ds = ConnectionManager::getDataSource('default');
        if (!($ds && $sql))
            return FALSE;
        $ds->begin();
        if (!$ds->execute($sql))
            return $ds->rollback() && false;
        else
            return $ds->commit();
    }

    /**
     *
     * @param <string> $sql well-formatted SQL string
     * @param <string> $tableName : name of class or table model
     * @return <boolean> FALSE: no object found
     * @return <array> Object Model: objects were found
     */
    protected function querySql($sql, $tableName = 'Model') {
        $ds = ConnectionManager::getDataSource('default');
        if (!($ds && $sql))
            return FALSE;
        $results = $ds->query($sql);
        if (empty($results))
            return false;/*
        if (!in_array($tableName, array('acos', 'aros', 'domain_info', 'reservation_info')))
            $tableName = 'stdClass';*/
        $result_obj = array();
        foreach ($results as &$row) {
            $obj = new $tableName();
            foreach ($row as $model => $modelRow)
                foreach ($modelRow as $key => $value) {
                    $obj->$key = $value;
                }
            $result_obj[] = $obj;
        }
        return $result_obj;
        /* if ($db->query($sql, $tableName)) {
          $result_obj = array();
          while ($db->hasNext()) {
          $result_obj[] = $db->next();
          }
          debug($result_obj);
          return $result_obj;
          } else {
          return FALSE;
          } */
    }

    private function normalizeStringArray($data = array()) {
        foreach ($data as &$item)
            $item = '"' . $item . '"';
        return $data;
    }

    function buildWhere($fields=array()) {
        $values = get_object_vars($this);
        $validInds = $this->getValidInds();

        if (!$validInds)
            return FALSE;

        $newValidInds = array();
        if ($fields) {
            foreach ($validInds as $vi) {
                if (array_search($vi, $fields) !== FALSE) {
                    $newValidInds[] = $vi;
                }
            }
        } else
            $newValidInds = $validInds;

        $whereArgs = array();
        foreach ($newValidInds as $vi) {
            if ($values[$vi] === NULL) {
                $whereArgs[] = "`$vi` IS NULL";
            } elseif ($values[$vi]) {
                if (is_array($values[$vi])) {
                    if ($this->attributes[$vi]->type == "VARCHAR")
                        $values[$vi] = $this->normalizeStringArray($values[$vi]);
                    $whereArgs[] = "`$vi` IN (" . implode(', ', $values[$vi]) . ")";
                } else {
                    if ($this->attributes[$vi]->type == "VARCHAR")
                        $values[$vi] = "\"" . $values[$vi] . "\"";
                    $whereArgs[] = "`$vi`=$values[$vi]";
                }
            }
        }

        if ($whereArgs)
            return implode(' AND ', $whereArgs);
        else
            return FALSE;
    }

    function getNextId($field) {
        $tableName = $this->getTableName();
        $whereString = $this->buildWhere();

        if ($whereString)
            $sql = "SELECT MAX($field) as $field from $tableName WHERE $whereString";
        else
            $sql = "SELECT MAX($field) as $field from $tableName";

        $result = $this->querySql($sql, $tableName);

        if ($result) {
            $last = $result[0]->{$field};
            return $last + 1;
        }
        return FALSE;
    }

    public function get($field=NULL, $useACL=TRUE) {
        if ($tmp = $this->fetch($useACL)) {
            if ($field)
                return $tmp[0]->{$field};
            else
                return $tmp[0];
        } else
            return FALSE;
    }

    public function fetchList() {
        if ($res = $this->fetch()) {

            $item = $res[0];

            $attr = $item->getValidInds();

            $temp = array();
            if (!empty($this->displayField)) {
                $temp[] = $item->{$this->displayField};
            } else
                foreach ($attr as $at_name) {
                    if (($item->attributes[$at_name]->usedInInsert) && !($item->attributes[$at_name]->forceUpdate))
                        $temp[] = "$at_name: " . $item->$at_name;
                }

            return implode("; ", $temp);
        }
        return false;
    }

}
