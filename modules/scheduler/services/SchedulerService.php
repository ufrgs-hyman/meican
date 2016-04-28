<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\services;

use meican\base\services\ConsoleService;

/**
 * Service to create and delete the scheduled tasks on OS system.
 * Currently only Unix systems are supported.
 *
 * @author Maurício Quatrin Guerreiro
 */
class SchedulerService {

    public function create(SchedulableTask $task) {
        ConsoleService::run("scheduler/task/create ".$task->id);
    }

    public function delete(SchedulableTask $task) {
        ConsoleService::run("scheduler/task/delete ".$task->id);
    }
}

?>