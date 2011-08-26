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
        unset($primaryKey);

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
            } else
                return FALSE; //sem acesso a nada
        } else { //sem ACL
            if ($whereArgsString)
                $sql = "SELECT * FROM `$tableName` WHERE $whereArgsString";
            else
                $sql = "SELECT * FROM `$tableName`";
        }
        return $this->fetchSql($sql, $tableName);
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
        return $this->querySql($sql);
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

        $resfetch = $this->fetchSql($sqlfetch, $tableName);

        Framework::debug('sql update', $sql);

        if (!$resfetch)
            return FALSE;
        else
            return $this->querySql($sql);
    }

//do updateto

    /**
     * @return <boolean> FALSE : on failed insertion
     * @return <object> Object : the inserted object
     */
    public function insert() {

        $classname = $this->getTableName();
        $values = get_object_vars($this);
        $isFirst = true;
        $sqlNames = "";
        $sqlValues = "";
        foreach ($this->attributes as $attribute) {
            $name = $attribute->name;
            if ($attribute->type == "VARCHAR") {
                $values[$name] = "'" . $values[$name] . "'";
            }
            if ($attribute->usedInInsert) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $sqlValues.=", ";
                    $sqlNames.=", ";
                }
                
                $sqlNames .= "`$name`";
                
                if ($values[$name] != "")
                    $sqlValues .= $values[$name];
                else
                    $sqlValues .= "NULL";
            }
        }
        $sql = "INSERT INTO `$classname` ($sqlNames) values ($sqlValues)";

        //Framework::debug('sql insert',$sql);
        //se inseriu com sucesso, retorna o objeto inserido
        if ($this->querySql($sql)) {
            if ($ret = $this->fetch(FALSE))
                return $ret[0];
            else
                return FALSE;
        }
        else
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

            $result = $this->fetchSql($sqlfetch, $tableName);

            if (!$result) //nao consigurira atualizar nada
                return FALSE;
        }

        return $this->querySql($sql);
    }

    /**
     *
     * @param <string> $sql : well-formatted SQL string
     * @param <string> $tableName : name of class or table model
     * @return <boolean> TRUE if query was success, FALSE otherwise
     */
    protected function querySql($sql) {
        $db = new Database();

        if (!($db && $sql))
            return FALSE;

        if ($db->query($sql))
            return TRUE;
        else {
            return FALSE;
        }
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

        if ($db->transactionSql($sql))
            return TRUE;
        else {
            return FALSE;
        }
    }

    /**
     *
     * @param <string> $sql : well-formatted SQL string
     * @param <string> $tableName : name of class or table model
     * @return <boolean> FALSE: no object found
     * @return <array> Object Model: objects were found
     */
    protected function fetchSql($sql, $tableName = 'Model') {
        $db = new Database();

        if (!($db && $sql))
            return FALSE;

        if ($db->query($sql, $tableName)) {
            if ($db->hasNext()) {
                $result_obj = array();
                while ($db->hasNext()) {
                    $result_obj[] = $db->next();
                }
                return $result_obj;
            } else
                return FALSE;
        } else {
            return FALSE;
        }
    }

    function buildWhere($implode = TRUE) {
        $values = get_object_vars($this);
        $validInds = $this->getValidInds();

        if (!$validInds)
            return FALSE;

        $whereArgs = array();
        foreach ($validInds as $vi) {
            if ($values[$vi]) {
                if ($this->attributes[$vi]->type == "VARCHAR")
                    $values[$vi] = "\"" . $values[$vi] . "\"";
                $whereArgs[] = "`$vi`=$values[$vi]";
            }
        }

        if ($whereArgs)
            if ($implode)
                return implode(' AND ', $whereArgs);
            else
                return $whereArgs;
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

        $result = $this->fetchSql($sql, $tableName);

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