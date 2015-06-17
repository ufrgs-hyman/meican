<?php

namespace app\modules\circuits\controllers\service;

use yii\console\Controller;
use Yii;
use app\components\CrontabManager;
use app\models\AutomatedTest;

class CrontabController extends Controller {
	
	public function beforeAction($action) {
		if (parent::beforeAction($action)) {
			return Yii::$app->id == "meican-console";
		} else {
			return false;
		}
	}
	
	private function getExecutionPath($testId) {
		return Yii::$app->basePath."/yii circuits/service/crontab/task ".$testId;
	}
	
	public function actionStart() {
		$crontab = new CrontabManager();
		$job = $crontab->newJob();
		$job->on('* * * * *');
		$job->doJob(Yii::$app->basePath."/yii circuits/service/crontab/listener");
		$crontab->add($job);
		$crontab->save(false);
		echo "Meican Crontab Service started\n";
	}
	
	public function actionStop() {
		$crontab = new CrontabManager();
		$job = $crontab->newJob();
		$job->on('* 1 * * *');
		$job->doJob("stopping service...");
		$crontab->add($job);
		$crontab->save(false);
		$this->delete($crontab, $job->id);
		echo "Meican Crontab Service stopped\n";
	}
	
    public function actionListener() {
    	$tests = AutomatedTest::find()->where(['crontab_changed'=> true])->orWhere(['crontab_id'=> null])->all();
    	if ($tests) {
	    	foreach ($tests as $test) {
	    		if ($test->crontab_id != null) $this->delete($test->crontab_id);
	    		
    			switch ($test->status) {
    				case AutomatedTest::STATUS_PROCESSING :
    					$this->create($test);
    					break;
    				case AutomatedTest::STATUS_ENABLED :
    					$this->create($test);
    					break;
    				case AutomatedTest::STATUS_DISABLED :
    					break;
    				case AutomatedTest::STATUS_DELETED :
    					$test->delete();
    			}
	    	}
    	}
    }
    
    private function create($test) {
    	$crontab = new CrontabManager();
    	$job = $crontab->newJob();
    	$job->on($test->crontab_frequency);
    	$job->doJob($this->getExecutionPath($test->id));
    	$crontab->add($job);
    	$crontab->save();
    	$test->crontab_id = $job->id;
    	$test->crontab_changed = false;
    	$test->status = AutomatedTest::STATUS_ENABLED;
    	$test->save();
    }
    
    public function actionTask($id) {
    	$test = AutomatedTest::findOne($id);
    	if($test) $test->execute();
    }
    
    private function delete($jobId) {
    	$crontab = new CrontabManager();
    	$crontab->deleteJob($jobId);
    	$crontab->save(false);
    }
}
