<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\scheduler\controllers;

use yii\console\Controller;
use Yii;

use meican\scheduler\components\CrontabManager;
use meican\scheduler\models\ScheduledTask;

/**
 * Controller module of the Scheduler Service. This controller
 * is used by:
 * - OS system when the tasks are executed.
 * - Scheduler Service to create, update or delete tasks.
 *
 * Currently only Unix systems are supported.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class TaskController extends Controller {

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (Yii::$app instanceof \yii\web\Application) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
        return true;
    }

    private function getExecutionPath() {
        return Yii::$app->basePath."/yii scheduler/task/execute";
    }
    
    public function actionCreate($id) {
        $task = ScheduledTask::findOneByJob($id);
        return $this->createCron($id, $task->freq, $this->getExecutionPath());
    }

    public function actionDelete($id) {
        return $this->deleteCron($id);
    }
    
    public function actionExecute($tag, $id) {
        $test = ScheduledTask::findOne(str_replace("job", "", $id));
        $test->execute();
    }

    private function createCron($id, $freq, $execPath) {
        $crontab = new CrontabManager();
        $job = $crontab->newJob();
        $job->id = $id;
        $job->on($freq);
        $job->doJob($execPath);
        $crontab->add($job);
        $crontab->save();
    }

    private function deleteCron($id) {
        $crontab = new CrontabManager();
        $crontab->deleteJob($id);
        $crontab->save(false);
    }
}
