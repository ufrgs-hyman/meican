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
 * Console controller used by OS crontab system to execute tasks or
 * by SchedulableTasks to create, update or delete tasks.
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
    
    public function actionCreate() {
        $test = new ScheduledTask;
        return $test->createTask($this->getExecutionPath());
    }

    public function actionDelete($id) {
        $test = ScheduledTask::findOneByJob($id);
        return $test->deleteTask();
    }
    
    public function actionExecute($tag, $id) {
        $test = ScheduledTask::findOneByJob($id);
        $test->execute();
    }
}
