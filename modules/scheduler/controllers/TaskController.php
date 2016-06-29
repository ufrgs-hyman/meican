<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\scheduler\controllers;

use yii\console\Controller;
use yii\web\ForbiddenHttpException;
use Yii;

use meican\scheduler\utils\CrontabManager;
use meican\scheduler\models\ScheduledTask;

/**
 * Console controller module of the Scheduler Service. This 
 * controller must be ONLY used by:
 * - Operational System to execute tasks.
 * - Scheduler Service to create, update or delete tasks.
 * - Tests involving scheduled tasks.
 *
 * To create scheduled tasks, is not required use directly
 * this service. Simply create a instance of ScheduledTask
 * and after save this service is called in sequence.
 *
 * Currently only Unix systems are supported.
 *
 * ScheduledTasks valid instances are required for all
 * actions in this controller.
 *
 * @author Maurício Quatrin Guerreiro
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
    
    /**
     * Create a cron associated to a ScheduledTask
     *
     * @param integer ScheduledTask id
     * @return integer status
     */
    public function actionCreate($taskId) {
        $task = ScheduledTask::findOne($taskId);
        $this->createCron($this->toCronId($taskId), $task->freq);
        return 0;
    }

    /**
     * Delete a cron associated to a ScheduledTask
     *
     * @param integer ScheduledTask id
     * @return integer status
     */
    public function actionDelete($taskId) {
        $this->deleteCron($this->toCronId($taskId));
        return 0;
    }
    
    /**
     * Execute a cron associated to a ScheduledTask
     *
     * @param string tag
     * @param string cron Id
     * @return integer status
     */
    public function actionExecute($tag, $cronId) {
        $task = ScheduledTask::findOne($this->toTaskId($cronId));
        if($task)
            $task->execute();
        else 
            $this->deleteCron($cronId);
        return 0;
    }

    private function toCronId($taskId) {
        return 'job'.$taskId;
    }

    private function toTaskId($cronId) {
        return str_replace("job", "", $cronId);
    }

    /**
     * Create a cron entry on Crontab table.
     *
     * @param string id
     * @param string freq
     */
    private function createCron($id, $freq) {
        $crontab = new CrontabManager();
        $job = $crontab->newJob();
        $job->id = $id;
        $job->on($freq);
        $job->doJob($this->getExecutionPath());
        $crontab->add($job);
        $crontab->save();
    }

    /**
     * Delete a cron entry on Crontab table.
     *
     * @param string id
     */
    private function deleteCron($id) {
        $crontab = new CrontabManager();
        $crontab->deleteJob($id);
        $crontab->save(false);
    }
}
