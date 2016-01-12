<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\topology\forms;

use Yii;
use yii\data\ActiveDataProvider;

use meican\topology\models\DiscoverySource;
use meican\topology\models\Service;
use meican\topology\components\NSIParser;
use meican\topology\controllers\services\DiscoveryClient;

class DiscoverySourceForm extends DiscoverySource {

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
            'freq_enabled' => Yii::t('topology', 'Autosync by recurrence'),
            'subscribe_enabled' => Yii::t('topology', 'Autosync by notification')
        ]);
    }

    public function validateSubscribe($att, $params) {
        switch ($this->protocol) {
            case Service::TYPE_NSI_DS_1_0: 
                if ($this->subscribe_enabled && $this->subscription_id == null) {
                    $this->subscription_id = DiscoveryClient::subscribe($this->url);
                } else if(!$this->subscribe_enabled) {
                    DiscoveryClient::unsubscribe($this->url, $this->subscription_id);
                    $this->subscription_id = null;
                }
                break;
            default: 
                $this->subscription_id = null;
                $this->provider_nsa = null;
                break;
        }
        return true;
    }

    public function validateUrl($att, $params) {
        switch ($this->type) {
            case DiscoverySource::DESC_TYPE_NSI:
                $parser = new NSIParser;  
                $parser->loadFile($this->url);
                switch ($this->protocol) {
                    case DiscoverySource::PROTOCOL_HTTP:
                        if (!$parser->isTD()) {
                            $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                            return false;
                        }
                        return true;
                        break;
                    case DiscoverySource::PROTOCOL_NSI_DS:
                        if (!$parser->isDS()) {
                            $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                            return false;
                        }
                        $parser->parseLocalProvider();
                        $this->provider_nsa = $parser->getData()['local']['nsa'];
                        return true;
                }
                break;
                
            case DiscoverySource::DESC_TYPE_NMWG: 
                $parser = new NMWGParser;  
                $parser->loadFile($this->url);
                if (!$parser->isTD()) {
                    $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                    return false;
                }
                return true;
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
