<?php

require_once 'MDB2.php';
include_once 'libs/Model/model.php';
include_once 'libs/Model/datasource.php';

class Database {
    private $result;
    private $classname;
    private $position;
    private $numRows;
    private $mdb2;

    public function Database() {
        /**
         * a conexão com o banco acontece na main
         * chama o método singleton apenas para obter a instância do MDB2
         */
        $datasource = Datasource::getInstance();
        $datasource->open();
        $this->mdb2 = $datasource->mdb2;//MDB2::singleton();

        if (MDB2::isError($this->mdb2)) {
            debug($this->mdb2->getMessage() . ", " . $this->mdb2->getDebugInfo());
            return FALSE;
        } else return TRUE;
    }
    
    public function insert($sql) {
        Datasource::logQuery($sql);
        $result = $this->mdb2->exec($sql);
        if (MDB2::isError($result)) {
            debug($result->getMessage() . ", " . $result->getDebugInfo());
            return FALSE;
        } else {
            return $this->mdb2->lastInsertId();
        }
    }
    
    public function exec($sql) {
        Datasource::logQuery($sql);
        $result = $this->mdb2->exec($sql);
        if (MDB2::isError($result)) {
            debug($result->getMessage() . ", " . $result->getDebugInfo());
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function query($sql, $classname='Model') {
        $result = $this->mdb2->query($sql);
        Datasource::logQuery($sql);
        if (MDB2::isError($result)) {
            debug($result->getMessage() . ", " . $result->getDebugInfo());
            return FALSE;
        } else {
            $this->result = $result;
            $this->classname = $classname;
            if (method_exists ($this->result, "numRows")) {
                $this->numRows = $this->result->numRows();
            } else {
                $this->numRows = 0;
            }

            $this->position = 0;
            return TRUE;
        }
    }

    public function transactionExec($sql) {
        Datasource::logQuery($sql);
        //Verifica se o Banco suporta transações
        if (!$this->mdb2->supports('transactions')) {
            debug("Erro: Banco não suporta transações.");
            return FALSE;
        }

        $sqlArray = explode(";", $sql);

        //Inicia transação
        $this->mdb2->beginTransaction();

        foreach($sqlArray as $sqlQuery) {
            if ($sqlQuery) {

                $result = $this->mdb2->exec($sqlQuery);

                if (MDB2::isError($result)) {
                    debug($result->getMessage() . ", " . $result->getDebugInfo());
                    //Caso ocorra erro, alterações são desfeitas pelo comando rollback() e desconecta do Banco
                    if ($this->mdb2->inTransaction()) {
                        debug("Erro: Alterações serão desfeitas");
                        $this->mdb2->rollback();
                    }
                    return FALSE;
                }
            }
        }
        //Se a transação foi concluída com sucesso, executa Commit() e desconecta do Banco
        if ($this->mdb2->inTransaction()) {
            $this->mdb2->commit();
            return TRUE;
        }
    }

    public function hasNext() {
        if ($this->position < $this->numRows)
            return TRUE;
        else return FALSE;
    }

    public function next() {
        $row = $this->result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $obj = new $this->classname;
        if ($this->classname == "Model") {
            foreach ($row as $key => $value) {
                $obj->$key = $value;
            }
        } else {
            foreach ($obj->attributes as $attribute) {
                $name = $attribute->name;
                if (array_key_exists($name, $row))
                    $obj->$name = $row[$name];
            }
        }
        $this->position++;
        return $obj;
    }

    public function getNumRows() {
        return $this->numRows;
    }

    static function mysql_replace($inp) {
        if (is_array($inp))
            return array_map(__METHOD__, $inp);

        if (!empty($inp) && is_string($inp)) {
            $inp = trim($inp);
            $return = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp, &$count);
            if ($count > 0)
                debug("trying sql injection");
            return htmlspecialchars($return);
        }

        return $inp;
    }
    
//    function getNextId($table) {
//        if (DatabaseObject::$mdb2 == null) {
//            DatabaseObject::$mdb2 = MDB2::connect(getDatabaseString());
//            if (MDB2::isError(DatabaseObject::$mdb2)) {
//                die (DatabaseObject::$mdb2->getMessage() . ", " . DatabaseObject::$mdb2->getDebugInfo());
//            }
//        }
//        return DatabaseObject::$mdb2->nextID($table);
//    }

//	static function translateFunction ($functionName, $args = null) {
//		switch ($functionName) {
//			case "CONDITION_DEV": {
//				if (DigistarSystem::getDefaultDatabase() == 'mysql') {
//					return "(A.dev_status_last_change > ".$args .") AS has_changed_status";
//				} else if (DigistarSystem::getDefaultDatabase() == 'oracle') {
//					return "CASE WHEN (A.dev_status_last_change > " . $args . ") THEN 1 ELSE 0 END AS has_changed_status";
//				}
//			}
//			case "CONDITION_TOP": {
//				if (DigistarSystem::getDefaultDatabase() == 'mysql') {
//					return "(A.tpl_status_last_change > ".$args .") AS has_changed_status ";
//				} else if (DigistarSystem::getDefaultDatabase() == 'oracle') {
//					return "CASE WHEN (A.tpl_status_last_change > " . $args . ") THEN 1 ELSE 0 END AS has_changed_status";
//				}
//			}
//		}
//	}

}
?>
