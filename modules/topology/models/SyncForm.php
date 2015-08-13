<?php

namespace app\modules\topology\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\TopologySynchronizer;
use app\models\Cron;
use app\models\Service;
use app\modules\topology\models\NSIParser;
use yii\base\Model;

class SyncForm extends TopologySynchronizer {

    public $freq;
    public $freq_enabled;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['freq'], 'safe'],
            [['freq_enabled'], 'integer'],
            [['url'], 'validateUrl'],
            [['subscribed'], 'validateSubscribe']
        ], parent::rules());
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'freq_enabled' => Yii::t('app', 'Enable Auto Sync'),
        ]);
    }

    public function validateSubscribe($att, $params) {
        switch ($this->type) {
            case Service::TYPE_NSI_TD_2_0: 
            case Service::TYPE_NMWG_TD_1_0: 
            case Service::TYPE_PERFSONAR_TS_1_0: 
                $this->subscribed = false;
                $this->provider_nsa = null;
            case Service::TYPE_NSI_DS_1_0: 
        }
        return true;
    }

    public function validateUrl($att, $params) {
        switch ($this->type) {
            case Service::TYPE_NSI_DS_1_0: 
                $parser = new NSIParser;  
                $parser->loadFile($this->url);
                if (!$parser->isDS()) {
                    $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                    return false;
                }
                $parser->parseLocalProvider();
                $this->provider_nsa = $parser->getData()['local']['nsa'];
                return true;
            case Service::TYPE_NSI_TD_2_0: 
                $parser = new NSIParser;  
                $parser->loadFile($this->url);
                if (!$parser->isTD()) {
                    $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                    return false;
                }
                return true;
            case Service::TYPE_NMWG_TD_1_0: 
                $parser = new NMWGParser;  
                $parser->loadFile($this->url);
                if (!$parser->isTD()) {
                    $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                    return false;
                }
                return true;
            case Service::TYPE_PERFSONAR_TS_1_0: 
                $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                return false;
        }
    }

    static function build($syncId) {
        $sync = self::findOne($syncId);
        $cron = Cron::findOneSyncTask($sync->id);
        if($cron) {
            $sync->freq = $cron->freq;
            $sync->freq_enabled = true;
        }

        return $sync;
    }

    public function saveCron() {
        $cron = Cron::findOneSyncTask($this->id);

        if ($this->freq_enabled) {
            if (!$cron) {
                $cron = new Cron;
                $cron->task_type = Cron::TYPE_SYNC;
                $cron->task_id = $this->id;
            } 
            $cron->status = Cron::STATUS_PROCESSING;
            $cron->freq = $this->freq;
            $cron->save();
        } else if ($cron) {
            $cron->delete();
        }
    }
}
