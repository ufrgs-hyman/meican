<?php

include_once 'libs/app.php';

class init extends App {

    public function init() {
        $this->appName = 'init';
        $this->defaultController = 'login';
    }

}

?>