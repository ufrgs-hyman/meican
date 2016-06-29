<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\scheduler\models;

use Yii;

use meican\base\utils\DateUtils;
use meican\scheduler\utils\SchedulableTask;

/**
 * Essa classe representa um objeto ScheduledTask,
 * também chamado de Tarefa Agendada. O Meican Scheduler Service
 * gerencia cada uma das ScheduledTasks ativas no sistema. 
 * Quaisquer classes podem ser executadas por agendamento a partir
 * deste objeto, sempre que implementem a interface SchedulableTask.
 *
 * @property integer $id
 * @property string $obj_class
 * @property string $obj_data
 * @property string $status
 * @property string $freq
 * @property string $last_run_at
 *
 * @author Maurício Quatrin Guerreiro
 */
class ScheduledTask extends \yii\db\ActiveRecord {

    const STATUS_ENABLED =      "ENABLED";
    const STATUS_DISABLED =     "DISABLED";
    const STATUS_PROCESSING =   "PROCESSING";

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
            [['freq', 'obj_class','obj_data','status'], 'required'],
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

    public function afterSave() {
        SchedulerService::create($this);
    }

    /**
     * Executa a tarefa agendada, criando um objeto do tipo
     * definido e enviando como argumento uma string $obj_data;
     */
    public function execute() {
        $this->last_run_at = DateUtils::now();
        $this->save();

        $obj = Yii::createObject($this->obj_class);
        if ($obj) {
            if($obj instanceof SchedulableTask) {
                $obj->execute($this->obj_data);
            }
        } else {
            $this->delete();
        }
    }
}
