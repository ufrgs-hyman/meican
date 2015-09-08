<?php

namespace app\modules\topology\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\TopologySynchronizer;
use app\models\Cron;
use app\models\Service;
use app\modules\topology\models\NSIParser;
use app\modules\topology\controllers\services\DiscoveryClient;

class SyncForm extends TopologySynchronizer {

    public $freq;
    public $freq_enabled;
    public $subscribe_enabled;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['freq'], 'safe'],
            [['freq_enabled'], 'boolean'],
            [['url'], 'validateUrl'],
            [['subscribe_enabled'], 'validateSubscribe']
        ], parent::rules());
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'freq_enabled' => Yii::t('topology', 'Enable Auto Sync'),
        ]);
    }

    public function validateSubscribe($att, $params) {
        switch ($this->type) {
            case Service::TYPE_NSI_TD_2_0: 
            case Service::TYPE_NMWG_TD_1_0: 
            case Service::TYPE_PERFSONAR_TS_1_0: 
                $this->subscription_id = null;
                $this->provider_nsa = null;
                break;
            case Service::TYPE_NSI_DS_1_0: 
                if ($this->subscribe_enabled && $this->subscription_id == null) {
                    $this->subscription_id = DiscoveryClient::subscribe($this->url);
                } else if(!$this->subscribe_enabled) {
                    DiscoveryClient::unsubscribe($this->url, $this->subscription_id);
                    $this->subscription_id = null;
                }
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
        
        if(!isset($sync)) return false;
        
        $cron = Cron::findOneSyncTask($sync->id);
        if($cron) {
            $sync->freq = $cron->freq;
            $sync->freq_enabled = true;
        }

        if ($sync->subscription_id) 
            $sync->subscribe_enabled = true;

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
