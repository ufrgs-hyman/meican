<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\base\services;

use meican\base\services\ConsoleService;

/**
 * Service to create and delete the scheduled tasks on OS system.
 * Currently only Unix systems are supported.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class SchedulerService {

    public function create($id) {
        ConsoleService::run("scheduler/task/create ".$id);
    }

    public function delete($id) {
        ConsoleService::run("scheduler/task/delete ".$id);
    }
}

?>