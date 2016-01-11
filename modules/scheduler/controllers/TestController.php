<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\scheduler\controllers;

use yii\web\Controller;
use Yii;

use meican\scheduler\components\CrontabManager;
use meican\scheduler\models\ScheduledTask;
use meican\base\services\ConsoleService;

/**
 * Console controller used by OS crontab system to execute tasks or
 * by SchedulableTasks to create, update or delete tasks.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class TestController extends Controller {

    public function actionRun() {
        return json_encode(ConsoleService::run("scheduler/task/create"));
    }
}
