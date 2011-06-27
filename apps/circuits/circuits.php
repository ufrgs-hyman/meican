<?php

include_once 'libs/app.php';

class circuits extends App {

    public function circuits() {
        $this->appName = 'circuits';
        $this->defaultController = 'reservations';
    }

}

?>