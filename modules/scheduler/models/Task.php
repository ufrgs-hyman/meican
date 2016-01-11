<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\scheduler\models;

use Yii;
use meican\base\components\DateUtils;

/**
 * This is the model class for table "{{%task}}".
 *
 * Essa classe representa um objeto do tipo Cron,
 * também chamado de Tarefa Agendada. O Meican possui
 * um serviço Crontab que gerencia cada um dos Crons 
 * existentes no sistema. Atualmente podem ser agendados
 * testes automatizados e sincronizações.
 *
 * @property integer $id
 * @property string $external_id
 * @property string $task_type
 * @property string $task_id
 * @property string $status
 * @property string $freq
 * @property string $last_run_at
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class Task extends \yii\db\ActiveRecord
{
    const STATUS_ENABLED =      "ENABLED";
    const STATUS_DISABLED =     "DISABLED";
    const STATUS_DELETED =      "DELETED";
    const STATUS_PROCESSING =   "PROCESSING";
    
    const TYPE_SYNC = "SYNC";
    const TYPE_TEST = "TEST";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cron}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['freq', 'task_type','task_id','status'], 'required'],
            [['status'], 'string'],
            [['last_run_at'], 'safe'],
            [['external_id'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'external_id' => Yii::t('app', 'External ID'),
            'status' => Yii::t('app', 'Status'),
            'last_run_at' => Yii::t('app', 'Last Run At'),
        ];
    }

    static function findOneSyncTask($id) {
        return self::find()->where(['task_type'=>self::TYPE_SYNC, 'task_id'=>$id])->one();
    }

    static function findTestTask($id) {
        return self::find()->where(['task_type'=>self::TYPE_TEST, 'task_id'=>$id]);
    }

    static function findOneTestTask($id) {
        return self::findTestTask($id)->one();
    }

    static function existsSyncTask($id) {
        return self::find()->where(['task_type'=>self::TYPE_SYNC, 'task_id'=>$id])
            ->andWhere(['in','status',[self::STATUS_PROCESSING, self::STATUS_ENABLED]])->count();
    }

    public function execute2() {
        $this->last_run_at = DateUtils::now();
        $this->save();

        switch ($this->task_type) {
            case self::TYPE_SYNC:
                $sync = TopologySynchronizer::findOne($this->task_id);
                if ($sync) {
                    $sync->execute();
                } else {
                    $this->status = self::STATUS_DELETED;
                    $this->save();
                }
                break;
            case self::TYPE_TEST:
                $res = AutomatedTest::findOne($this->task_id);
                if ($res) {
                    $res->execute();
                } else {
                    $this->status = self::STATUS_DELETED;
                    $this->save();
                }
                break;
        }
    }

    public function execute() {
        $test = new \meican\scheduler\Test;
        return $test->execute();
    }
}
