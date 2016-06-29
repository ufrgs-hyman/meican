<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\scheduler\services;

use meican\base\services\ConsoleService;
use meican\scheduler\models\ScheduledTask;

/**
 * Service to create and delete the scheduled tasks on OS system.
 *
 * Currently only Unix systems are supported.
 *
 * @author Maurício Quatrin Guerreiro
 */
class SchedulerService {

    public static function create(ScheduledTask $task) {
        ConsoleService::run("scheduler/task/create ".$task->id);
    }

    public static function delete(ScheduledTask $task) {
        ConsoleService::run("scheduler/task/delete ".$task->id);
    }

    public static function update(ScheduledTask $task) {
        ConsoleService::run("scheduler/task/delete ".$task->id);
        ConsoleService::run("scheduler/task/create ".$task->id);
    }
}

?>