<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%topo_sync_event}}".
 *
 * @property integer $id
 * @property string $started_at
 * @property string $status
 * @property integer $progress
 * @property integer $sync_id
 *
 * @property TopologySynchronizer $sync
 */
class TopologySyncEvent extends \yii\db\ActiveRecord
{
    const STATUS_INPROGRESS = "INPROGRESS";
    const STATUS_SUCCESS = "SUCCESS";
    const STATUS_FAILED = "FAILED";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%topo_sync_event}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['started_at', 'status', 'progress', 'sync_id'], 'required'],
            [['started_at'], 'safe'],
            [['status'], 'string'],
            [['progress', 'sync_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'started_at' => Yii::t('app', 'Started At'),
            'status' => Yii::t('app', 'Status'),
            'progress' => Yii::t('app', 'Progress'),
            'sync_id' => Yii::t('app', 'Sync ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSynchronizer()
    {
        return $this->hasOne(TopologySynchronizer::className(), ['id' => 'sync_id']);
    }

    public function applyChanges() {
        //NECESSARIO POR UM BUG NO CONTROLE DE MEMORIA DO YII
        //ELE NAO LIBERA A MEMORIA USADA NO LOG DE CADA APPLYCHANGE E ACABA EM FATAL ERROR
        $log = Yii::$app->log;
        foreach ($log->targets as $logTarget) { 
            $logTarget->enabled = false;
        }
        
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_DOMAIN);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_PROVIDER);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_PEERING);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_SERVICE);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_NETWORK);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_DEVICE);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_BIPORT);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_UNIPORT);
        $this->applyChangesByType(TopologyChange::ITEM_TYPE_LINK);

        $log = Yii::$app->log;
        foreach ($log->targets as $logTarget) { 
            $logTarget->enabled = true;
        }
    }

    private function applyChangesByType($type) {
        $changes = TopologyChange::find()
            ->where(['sync_event_id'=>$this->id, 'item_type'=>$type])
            ->andWhere(['in','status',[TopologyChange::STATUS_FAILED,TopologyChange::STATUS_PENDING]])->all();
        foreach ($changes as $change) {
            $change->apply();
        }
    }
}
