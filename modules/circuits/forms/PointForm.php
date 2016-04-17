<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

use meican\circuits\models\Connection;

/**
 * @author Maurício Quatrin Guerreiro
 */
class PointForm extends Model {
    
    public $domain;
    public $network;
    public $device;
    public $port;
    public $vlan;
    public $vlan_text;
    public $urn;

    public function rules() {
        return [
            [['domain', 'device', 'port','vlan','urn','vlan_text'],'required'],
            [['network'],'safe']
        ];
    }

    public function attributeLabels() {
        return [
            'domain' => Yii::t("circuits", 'Domain'),
            'network' =>  Yii::t("circuits", 'Network'),
            'device' =>  Yii::t("circuits", 'Device'),
            'port' =>  Yii::t("circuits", 'Port'),
            'vlan' =>  Yii::t("circuits", 'VLAN'),
            'vlan_text' =>  Yii::t("circuits", 'VLAN'),
            'urn' =>  Yii::t("circuits", 'URN'),
        ];
    }
}