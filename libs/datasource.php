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
            Framework::debug($this->mdb2->getMessage() . ", " . $this->mdb2->getDebugInfo());
            die($this->mdb2->getMessage());
        }
        return true;
    }

    public function close() {
        if (empty($this->mdb2))
            return;
        $this->mdb2->disconnect();
    }

    private function __destruct() {
        $this->close();
    }

    static function &getInstance() {
        static $instance = array();

        if (!$instance) {
            $instance[0] = & new Datasource();
        }
        return $instance[0];
    }

}
