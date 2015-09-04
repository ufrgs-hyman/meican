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
                case AaaPreference::AAA_FEDERATION_GROUP:
                    $this->group = $pref->value;                    
                    break;
                case AaaPreference::AAA_FEDERATION_DOMAIN:
                    $this->domain = $pref->value;                
                    break;
                case AaaPreference::AAA_FEDERATION_ENABLED:
                    $this->status = $pref->value;                     
                    break;
                default:
                    break;
            }
        }
    }

    public function save() {
        $pref = AaaPreference::findOne(AaaPreference::AAA_FEDERATION_GROUP);
        $pref->value = $this->group;
        if(!$pref->save()) return false;

        $pref = AaaPreference::findOne(AaaPreference::AAA_FEDERATION_DOMAIN);
        $pref->value = $this->domain;
        if(!$pref->save()) return false;

        $pref = AaaPreference::findOne(AaaPreference::AAA_FEDERATION_ENABLED);
        $pref->value = $this->status;
        if(!$pref->save()) return false;

        return true;
    }
}

?>