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
    public $acceptRelease = false;

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
        $oldStart = Connection::find()->where(['id'=>$this->id])->asArray()->select(['start'])->one()['start'];
        $start = DateUtils::localToUTC($this->start);

        if($start >= DateUtils::localToUTC($this->end)) 
            $this->addError('end', "Start time must be before end time");
        elseif($oldStart != $start && !$this->acceptRelease) 
            $this->addError('start', "A circuit interruption is required to change the start time.");
    }

    public function validateBandwidth($attr, $params) {
        $oldBand = Connection::find()->where(['id'=>$this->id])->asArray()->select(['bandwidth'])->one()['bandwidth'];
        if (!is_numeric($this->bandwidth) || !is_integer(intval($this->bandwidth)) || $this->bandwidth < 1) {
            $this->addError('bandwidth', "Invalid bandwidth value");
        } elseif($oldBand != $this->bandwidth && !$this->acceptRelease)
            $this->addError('bandwidth', "A circuit interruption is required to change the bandwidth.");
    }
    
    public function save() {
        if($this->validate()) {
            $changes = [];

            $conn = Connection::findOne($this->id);
            if ($this->acceptRelease) {
                if ($conn->getStartDateTime() != DateUtils::fromLocal($this->start)) {
                    $changes['needRelease'] = true;
                    $changes['start'] = DateUtils::localToUTC($this->start);
                }

                if ($conn->bandwidth != $this->bandwidth) {
                    $changes['needRelease'] = true;
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