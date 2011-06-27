<?php

include_once 'libs/app.php';

class domain extends App {

    public function domain() {
        $this->appName = 'domain';
        $this->defaultController = 'urns';
    }

}

?>