<?php

require_once 'MDB2.php';
include_once 'libs/attribute.inc';
include_once 'libs/database.inc';
include_once 'libs/acl_loader.inc';

class Model {

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
        unset($validInds);
        foreach ($this->attributes as $attribute) {
            $validInd[] = $attribute->name;
        }

        if (!isset($validInd))
            return FALSE;
        else
            return $validInd;
    }

    /**
     *
     * @param <type> $options
     * @return <boolean> FALSE: no object found
     * @return <array> Object Model: objects were found
     */
    public function fetch($useACL = true) {
        unset($allowPks);
        $tableName = $this->getTableName();
        $whereArgsString = $this->buildWhere();

        if ($useACL) {
            $acl = new AclLoader();
            $allowPks = $acl->getAllowedPKey('read', $tableName);

            if ($allowPks) {
                $inString = implode(',', $allowPks);
                $pk = $this->getPrimaryKey();
                if ($whereArgsString)
                    $sql = "SELECT * FROM `$tableName` WHERE $whereArgsString AND $pk IN ($inString)";
                else
                    $sql = "SELECT * FROM `$tableName` WHERE $pk IN ($inString)";
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
        //Framework::debug("fetch",$sql);
        return $this->querySql($sql, $tableName);
    }

    /**
     * Utiliza a PRIMARY_KEY do modelo para selecionar a linha a ser alterada.
     * Esse atributo precisa estar setado.
     * Os demais atributos que estiverem setados serão alterados. Os atributos que não estiverem setados
     * serão mantidos.
     *
     * @return <boolean> TRUE if update was success, FALSE otherwise
     */
    public function update() {

        //algoritmo simplificado:
        //achar a chave primária
        //fazer um fetch com o valor da chave primária setado no controller
        //alterar SOMENTE os atributos que foram setados no controller e deixar os demais inalterados
        // inicia objeto temporário para manter as informações do objeto original


        unset($validInd);

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
                Framework::debug('set all the primary keys to update.', $pk);
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
         */
        if (!$changed)
            return FALSE;

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
            if ($attribute->type == "VARCHAR") {
                $values[$name] = "'" . $values[$name] . "'";
            }
            if ($attribute->usedInUpdate) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $sql.=", ";
                }
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
        //Framework::debug('update',$sql);
        return $this->execSql($sql);
    }

    public function updateTo($alt = Array(), $useACL = TRUE) {

        if (!$alt)
            return FALSE;


        $tableName = $this->getTableName();
        $values = get_object_vars($this);

        $validInds = $this->getValidInds();
        unset($setArgs);
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

        if ($useACL) {

            $acl = new AclLoader();
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

        Framework::debug('sql update', $sql);

        if (!$resfetch)
            return FALSE;
        else
            return $this->execSql($sql);
    }

//do updateto

    /**
     * @return <boolean> FALSE : on failed insertion (FAILED)
     * @return <object> Object : the inserted object if it was possible to find it (SUCCESS)
     * @return <boolean> TRUE : the object was inserted, but it was not possible to find it (SUCCESS)
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
                        $sqlValues .= "'".$values[$name]."'";
                    else
                        $sqlValues .= $values[$name];
                }
            }
        }
        $sql = "INSERT INTO `$classname` ($sqlNames) values ($sqlValues)";

        //Framework::debug('sql insert',$sql);
        
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
            Framework::debug("objeto inserido mas não encontrado");
            return TRUE;
        } else
            // objeto não inserido, retorna FALSE
            return FALSE;
    }

    /**
     *
     * @return <boolean> TRUE if delete was success, FALSE otherwise
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

        Framework::debug('todelete', $toDelete);
        if (!$toDelete) {
            return FALSE;
        }

        $values = get_object_vars($this);
        $whereArgs = array();
        if ($useACL) {
            $acl = new AclLoader();
            $restr = $acl->getAllowedPKey('delete', $tableName);

            //delete nao permite where do tipo IN
            foreach ($toDelete as $d) {
                Framework::debug('fe', $d);
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
     * @param <string> $sql : well-formatted SQL string
     * @return <boolean> Object ID if insert was successful, FALSE otherwise
     */
    protected function insertSql($sql) {
        $db = new Database();

        if (!($db && $sql))
            return FALSE;

        return $db->insert($sql);
    }

   /**
     *
     * @param <string> $sql : well-formatted SQL string
     * @return <boolean> TRUE if exec was successful, FALSE otherwise
     */
    protected function execSql($sql) {
        $db = new Database();

        if (!($db && $sql))
            return FALSE;

        return $db->exec($sql);
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
        $db = new Database();

        if (!($db && $sql))
            return FALSE;

        return $db->transactionExec($sql);
    }

   /**
     *
     * @param <string> $sql : well-formatted SQL string
     * @param <string> $tableName : name of class or table model
     * @return <boolean> FALSE: no object found
     * @return <array> Object Model: objects were found
     */
    protected function querySql($sql, $tableName = 'Model') {
        $db = new Database();

        if (!($db && $sql))
            return FALSE;

        if ($db->query($sql, $tableName)) {
            $result_obj = array();
            if ($db->hasNext()) {
                while ($db->hasNext()) {
                    $result_obj[] = $db->next();
                }
                return $result_obj;
            } else
                return $result_obj;
        } else {
            return FALSE;
        }
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
                if ($this->attributes[$vi]->type == "VARCHAR")
                    $values[$vi] = "\"" . $values[$vi] . "\"";
                $whereArgs[] = "`$vi`=$values[$vi]";
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

    public function get($field=false) {
        $tmp = $this->fetch();
        if ($field)
            return $tmp[0]->{$field};
        else
            return $tmp[0];
    }

}

?>