<?php

class Datasource {

    var $mdb2 = null;

    public function open() {
        if ($this->mdb2)
            return true;
        $databaseConfs = Configure::read('database');
        $conf = $databaseConfs[Configure::read('defaultDatabase')];

        $this->mdb2 = MDB2::singleton($conf);
        if (MDB2::isError($this->mdb2)) {
            debug($this->mdb2->getMessage() . ", " . $this->mdb2->getDebugInfo());
            die($this->mdb2->getMessage());
        }
        return true;
    }

    public function close() {
        if (empty($this->mdb2))
            return;
        $this->mdb2->disconnect();
    }

    static function &getInstance() {
        static $instance = array();

        if (!$instance) {
            $instance[0] = & new Datasource();
        }
        return $instance[0];
    }
    
    static $queries = array();
    static function logQuery($query, $error = null, $affected = null, $numRows = null, $took = null){
        $took = $took*1000;
        self::$queries[] = compact('query', 'error', 'affected', 'numRows', 'took');
    }
    
    static function getQueries(){
        function qsum($v, $q){
                    return $q['took']+$v;
                };
        return
            array(
                'log' => self::$queries,
                'count' => count(self::$queries),
                'time' => array_reduce(self::$queries, 'qsum', 0)
            );
    }

}
