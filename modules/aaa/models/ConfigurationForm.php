<?php

namespace app\modules\aaa\models;

use yii\base\Model;
use Yii;
use app\components\DateUtils;

class ConfigurationForm extends Model {
    
    public $domain;
    public $group;
    public $status;
    
    public function rules() {
        return [
            [['domain','group','status'], 'required'],
            [['domain','group','status'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'domain' => Yii::t('circuits', 'Domain'),
            'group' => Yii::t('circuits', 'Group'),
            'status' => Yii::t('circuits', 'Status'),
        ];
    }

    public function setPreferences($prefs) {
        foreach ($prefs as $pref) {
            switch ($pref->name) {
                case FederationPreference::FEDERATION_GROUP:
                    $this->group = $pref->value;                    
                    break;
                case FederationPreference::FEDERATION_DOMAIN:
                    $this->domain = $pref->value;                
                    break;
                case FederationPreference::FEDERATION_STATUS:
                    $this->status = $pref->value;                     
                    break;
                default:
                    break;
            }
        }
    }

    public function save() {
        $pref = FederationPreference::findOne(FederationPreference::FEDERATION_GROUP);
        $pref->value = $this->group;
        if(!$pref->save()) return false;

        $pref = FederationPreference::findOne(FederationPreference::FEDERATION_DOMAIN);
        $pref->value = $this->domain;
        if(!$pref->save()) return false;

        $pref = FederationPreference::findOne(FederationPreference::FEDERATION_STATUS);
        $pref->value = $this->status;
        if(!$pref->save()) return false;

        return true;
    }
}

?>