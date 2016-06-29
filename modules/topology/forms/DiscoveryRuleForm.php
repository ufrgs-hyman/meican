<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\topology\forms;

use Yii;
use yii\data\ActiveDataProvider;

use meican\topology\models\DiscoveryRule;
use meican\topology\models\Service;
use meican\nsi\DiscoveryClient;
use meican\nsi\NSIParser;
use meican\nmwg\NMWGParser;
use meican\scheduler\models\ScheduledTask;

/**
 * @author Maurício Quatrin Guerreiro
 */
class DiscoveryRuleForm extends DiscoveryRule {

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
            'freq_enabled' => Yii::t('topology', 'Discover by recurrence'),
            'subscribe_enabled' => Yii::t('topology', 'Discover by notification')
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
            case DiscoveryRule::DESC_TYPE_NSI:
                $parser = new NSIParser;  
                $parser->loadFile($this->url);
                switch ($this->protocol) {
                    case DiscoveryRule::PROTOCOL_HTTP:
                        if (!$parser->isTD()) {
                            $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                            return false;
                        }
                        return true;
                        break;
                    case DiscoveryRule::PROTOCOL_NSI_DS:
                        if (!$parser->isDS()) {
                            $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                            return false;
                        }
                        $parser->parseLocalProvider();
                        $this->provider_nsa = $parser->getData()['local']['nsa'];
                        return true;
                }
                break;
                
            case DiscoveryRule::DESC_TYPE_NMWG: 
                $parser = new NMWGParser;  
                $parser->loadFile($this->url);
                if (!$parser->isTD()) {
                    $this->addError('', 'The inserted URL does not contain a valid service. Please try again.');
                    return false;
                }
                return true;
        }
    }

    static function loadFromDB($id) {
        $rule = parent::findOne($id);
        
        if(!isset($rule)) return null;
        
        $task = ScheduledTask::findOne(['obj_data'=>$id, 'obj_class'=>'meican\topology\models\DiscoveryTask']);
        if($task) {
            $rule->freq = $task->freq;
            $rule->freq_enabled = true;
        }

        if ($rule->subscription_id) 
            $rule->subscribe_enabled = true;

        return $rule;
    }

    public function afterSave($insert, $changedAttributes) {
        $task = ScheduledTask::findOne(['obj_data'=>$this->id, 'obj_class'=>'meican\topology\models\DiscoveryTask']);

        if ($this->freq_enabled) {
            if (!$task) {
                $task = new ScheduledTask;
                $task->obj_class = 'meican\topology\models\DiscoveryTask';
                $task->obj_data = $this->id;
            } 
            $task->freq = $this->freq;
            $task->save();
        } else if ($task) {
            $task->delete();
        }
    }
}
