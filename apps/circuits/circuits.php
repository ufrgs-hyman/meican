<?php

include_once 'libs/app.php';

class circuits extends App {

    public function circuits() {
        $this->appName = 'circuits';
        $this->defaultController = 'reservations';
    }

    public function getMenu() {
        return array(10 => new MenuItem(array(
                'label' => _("Circuits"),
                'sub' => array(
                    new MenuItem(array(
                        'label' => _("New"),
                        'model' => 'urn_info',
                        'right' => 'create',
                        'url' => array('app' => $this->appName, 'controller' => 'reservations', 'action' => 'add_form')
                    )),
                    new MenuItem(array(
                        'label' => _("Status"),
                        'model' => 'reservation_info',
                        'url' => array('app' => $this->appName, 'controller' => 'reservations', 'action' => 'status')
                    )),
                    new MenuItem(array(
                        'label' => _("History"),
                        'model' => 'reservation_info',
                        'url' => array('app' => $this->appName, 'controller' => 'reservations', 'action' => 'history')
                    )),
                )
            )));
    }

    function getDashboard() {
        return array(
            10 => new MenuItem(array(
                'label' => _("New Reservation"),
                'model' => 'urn_info',
                'right' => 'create',
                'url' => array('app' => $this->appName, 'controller' => 'reservations', 'action' => 'add_form'),
                'image' => 'webroot/img/new_reservation.png'
            )),
            20 => new MenuItem(array(
                'label' => _("Reservations"),
                'model' => 'reservation_info',
                'url' => array('app' => $this->appName, 'controller' => 'reservations', 'action' => 'status'),
                'image' => 'webroot/img/reservations_list.png'
            ))
        );
    }

}

?>