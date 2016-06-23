<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\services;

/**
 * Interface que representa um serviço de requisição de circuitos.
 *
 * @author Maurício Quatrin Guerreiro
 */
interface Requester {
    
    public function create();

    public function update();

    public function commit();

    public function read();

    public function provision();

    public function release();
}