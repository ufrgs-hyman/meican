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
    public $acceptRelease = true;

    public function rules() {
        return [
            [['id', 'end', 'acceptRelease'],'required'],
            [['id'], 'integer'],
            [['start'], 'safe'],
            [['bandwidth'], 'validateBandwidth'],
            [['end'], 'validateDateRange'],
        ];
    }

    public function attributeLabels() {
        return [
            'bandwidth' => Yii::t("circuits", 'Bandwidth'),
            'start' =>  Yii::t("circuits", 'Start time'),
            'end' =>  Yii::t("circuits", 'End time'),
            'acceptRelease' => Yii::t("circuits", 'If required, I do accept the circuit interruption'),
        ];
    }

    public function validateDateRange($attr, $params) {
        if($this->acceptRelease) {
            $start = DateUtils::localToUTC($this->start);
            if($start >= DateUtils::localToUTC($this->end)) {
                $this->addError('end', "Start time must be before end time");
                return;
            }

            $oldStart = Connection::find()->where(['id'=>$this->id])->asArray()->select(['start'])->one()['start'];
            if($oldStart != $start && DateUtils::now() > $start) 
                $this->addError('end', "Start time can not be changed in an active circuit.");
        } else {
            $oldStart = Connection::find()->where(['id'=>$this->id])->asArray()->select(['start'])->one()['start'];
            if($oldStart >= DateUtils::localToUTC($this->end)) 
                $this->addError('end', "Start time must be before end time");
        }
    }

    public function validateBandwidth($attr, $params) {
        if (!is_numeric($this->bandwidth) || !is_integer(intval($this->bandwidth))) {
            $this->addError('bandwidth', "Invalid bandwidth value");
        }
    }
    
    public function save() {
        if($this->validate()) {
            $conn = Connection::findOne($this->id);
            $conn->version = $conn->version + 1;
            if ($this->acceptRelease) {
                $conn->start = DateUtils::localToUTC($this->start);
                $conn->bandwidth = $this->bandwidth;
            }
            $conn->finish = DateUtils::localToUTC($this->end);
            return $conn->save();
        }
        return false;
    }
}