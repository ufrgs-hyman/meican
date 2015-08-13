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
}
