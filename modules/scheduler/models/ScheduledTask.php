<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\scheduler\models;

use Yii;

use meican\base\components\DateUtils;
use meican\scheduler\components\CrontabManager;

/**
 * Essa classe representa um objeto ScheduledTask,
 * também chamado de Tarefa Agendada. O Meican Scheduler Service
 * gerencia cada uma das ScheduledTasks ativas no sistema. 
 * Quaisquer classes podem ser executadas por agendamento a partir
 * deste objeto, sempre que implementem a interface SchedulableTask.
 *
 * @property integer $id
 * @property string $obj_class
 * @property string $obj_id
 * @property string $status
 * @property string $freq
 * @property string $last_run_at
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ScheduledTask extends \yii\db\ActiveRecord
{
    const STATUS_ENABLED =      "ENABLED";
    const STATUS_DISABLED =     "DISABLED";
    const STATUS_DELETED =      "DELETED";
    const STATUS_PROCESSING =   "PROCESSING";

    public $executionPath;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sche_task}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['freq', 'obj_class','obj_id','status'], 'required'],
            [['status'], 'string'],
            [['last_run_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'status' => Yii::t('app', 'Status'),
            'last_run_at' => Yii::t('app', 'Last Run At'),
        ];
    }

    static function findOneByJob($jobId) {
        return self::findOne(str_replace("job", "", $jobId));
    }

    

    public function execute() {
        $this->last_run_at = DateUtils::now();
        $this->save();

        $obj = Yii::createObject($this->obj_class)::findOne($this->task_id);
        if ($obj) {
            if($obj instanceof SchedulableTask) {
                $obj->execute();
            }
        } else {
            $this->delete();
        }
    }

    public function createTask() {
        $this->status = ScheduledTask::STATUS_PROCESSING;
        $this->obj_class = 'sd';
        $this->obj_id = 23;
        $this->freq = '* * 5 1 0';
        $this->save();

        $crontab = new CrontabManager();
        $job = $crontab->newJob();
        $job->id = $this->id;
        $job->on($this->freq);
        $job->doJob($this->executionPath);
        $crontab->add($job);
        $crontab->save();
    }

    public function createJob($id, $cmd, $freq) {
        $crontab = new CrontabManager();
        $job = $crontab->newJob();
        $job->id = $id;
        $job->on($freq);
        $job->doJob($cmd);
        $crontab->add($job);
        $crontab->save();
    }

    public function deleteJob($id) {
        $crontab = new CrontabManager();
        $crontab->deleteJob($id);
        $crontab->save(false);
    }
}
