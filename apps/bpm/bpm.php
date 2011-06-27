<?php

include_once 'libs/app.php';

class bpm extends App {

    public function bpm() {
        $this->appName = 'bpm';
        $this->defaultController = 'requests';
    }

}

?>