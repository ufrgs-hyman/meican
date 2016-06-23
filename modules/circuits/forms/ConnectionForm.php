<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\forms;

use yii\base\Model;
use Yii;

use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionEvent;
use meican\base\utils\DateUtils;

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
            $changes = [];

            $conn = Connection::findOne($this->id);
            if ($this->acceptRelease) {
                $changes = ['release'=> true];
                if ($conn->getStartDateTime() != DateUtils::fromLocal($this->start)) {
                    $changes['start'] = DateUtils::localToUTC($this->start);
                }

                if ($conn->bandwidth != $this->bandwidth) {
                    $changes['bandwidth'] = $this->bandwidth;
                }
            }

            if ($conn->getEndDateTime() != DateUtils::fromLocal($this->end)) {
                $changes['end'] = DateUtils::localToUTC($this->end);
            }

            if((count($changes) > 0)) {
                return $conn->buildEvent(ConnectionEvent::TYPE_USER_UPDATE)
                    ->setData(json_encode($changes))
                    ->setAuthor(Yii::$app->user->getId())
                    ->save();
            } 
            return false;
        }
        return false;
    }
}