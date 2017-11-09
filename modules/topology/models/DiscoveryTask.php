<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\models;

use Yii;

use meican\scheduler\utils\SchedulableTask;
use meican\topology\services\DiscoveryService;

/**
 * Esta classe representa uma execução realizada pelo 
 * serviço de descobrimento (DiscoveryService). A partir
 * de uma DiscoveryRule, uma execução pode gerar Changes
 * representando as diferenças percebidas na topologia do
 * provedor comparado a topologia local.
 *
 * @property integer $id
 * @property string $started_at
 * @property string $status
 * @property integer $progress
 * @property integer $sync_id
 *
 * @property DiscoveryRule $rule
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class DiscoveryTask extends \yii\db\ActiveRecord implements SchedulableTask {

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
            'started_at' => Yii::t('app', 'Started at'),
            'status' => Yii::t('app', 'Status'),
            'progress' => Yii::t('app', 'Progress'),
            'sync_id' => Yii::t('app', 'Sync ID'),
        ];
    }

    public function getChanges()
    {
        return $this->hasMany(Change::className(), ['sync_event_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(DiscoveryRule::className(), ['id' => 'sync_id']);
    }

    public function execute($ruleId) {
        $rule = DiscoveryRule::findOne($ruleId);
        $ds = new DiscoveryService; 
        $ds->execute($this, $rule);
    }

    public function applyChanges() {
        //NECESSARIO POR UM BUG NO CONTROLE DE MEMORIA DO YII
        //ELE NAO LIBERA A MEMORIA USADA NO LOG DE CADA APPLYCHANGE E ACABA EM FATAL ERROR
        $log = Yii::$app->log;
        foreach ($log->targets as $logTarget) { 
            $logTarget->enabled = false;
        }
        
        $this->applyChangesByType(Change::ITEM_TYPE_DOMAIN);
        $this->applyChangesByType(Change::ITEM_TYPE_PROVIDER);
        $this->applyChangesByType(Change::ITEM_TYPE_PEERING);
        $this->applyChangesByType(Change::ITEM_TYPE_SERVICE);
        $this->applyChangesByType(Change::ITEM_TYPE_NETWORK);
        //$this->applyChangesByType(Change::ITEM_TYPE_DEVICE);
        $this->applyChangesByType(Change::ITEM_TYPE_BIPORT);
        $this->applyChangesByType(Change::ITEM_TYPE_UNIPORT);
        $this->applyChangesByType(Change::ITEM_TYPE_LINK);

        $log = Yii::$app->log;
        foreach ($log->targets as $logTarget) { 
            $logTarget->enabled = true;
        }
    }

    private function applyChangesByType($type) {
        $changes = Change::find()
            ->where(['sync_event_id'=>$this->id, 'item_type'=>$type])
            ->andWhere(['in','status',[Change::STATUS_FAILED,Change::STATUS_PENDING]])->all();
        foreach ($changes as $change) {
            $change->apply();
        }
    }
}
