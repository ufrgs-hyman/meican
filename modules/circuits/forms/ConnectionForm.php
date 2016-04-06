<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

use meican\circuits\models\Connection;
use meican\base\components\DateUtils;

/**
 * @author Maurício Quatrin Guerreiro
 */
class ConnectionForm extends Model {
    
    public $id;
    public $bandwidth;
    public $start;
    public $end;

    public function rules() {
        return [
            [['id', 'bandwidth', 'start','end'],'required'],
            [['id'], 'integer'],
            [['bandwidth'], 'validateBandwidth'],
            [['start'], 'validateDateRange'],
            [['end'], 'validateDateRange']
        ];
    }

    public function attributeLabels() {
        return [
            'bandwidth' => Yii::t("circuits", 'Bandwidth'),
            'start' =>  Yii::t("circuits", 'Start time'),
            'end' =>  Yii::t("circuits", 'End time'),
        ];
    }

    public function validateDateRange($attr, $params) {
        if(DateUtils::localToUTC($this->start) >= DateUtils::localToUTC($this->end))
            $this->addError($attr, "Start time must be before end time");
    }

    public function validateBandwidth($attr, $params) {
        if (!is_numeric($this->bandwidth) || !is_integer(intval($this->bandwidth))) {
            $this->addError($this->bandwidth, "Invalid bandwidth value");
        }
    }
    
    public function save() {
        if($this->validate()) {
            $conn = Connection::findOne($this->id);
            $conn->version = $conn->version + 1;
            $conn->start = DateUtils::localToUTC($this->start);
            $conn->finish = DateUtils::localToUTC($this->end);
            $conn->bandwidth = $this->bandwidth;
            return $conn->save();
        }
        return false;
    }
}