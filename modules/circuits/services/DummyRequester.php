<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\services;

use meican\circuits\models\ConnectionEvent;

/**
 * Serviço que realiza requisições de circuitos em um ambiente simulado.
 *
 * @author Maurício Quatrin Guerreiro
 */
class DummyRequester implements Requester {

    public $conn;

    function __construct($conn=null){
        $this->conn = $conn;
    }

    public function create() {
        sleep(4);
        $date = new \DateTime();
        $this->conn->external_id = 'f'.$date->format('YmdHis');
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE)->save();
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_RESPONSE)->save();
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_RESERVE_CONFIRMED)->save();
        $this->conn->confirmCreate();
        $this->conn->confirmCreatePath();
    }

    public function commit() {
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT)->save();
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_COMMIT_CONFIRMED)->save();
        $this->conn->confirmCommit();
    }

    public function info() {
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_SUMMARY)->save();
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_SUMMARY_CONFIRMED)->save();
        $this->conn->confirmSummary();
    }

    public function update() {
    }

    public function provision() {
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_PROVISION)->save();
        sleep(1);
        $this->conn->buildEvent(ConnectionEvent::TYPE_NSI_PROVISION_CONFIRMED)->save();
        $this->conn->confirmProvision();
    }
}