<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%automated_test}}".
 *
 * @property integer $id
 * @property string $crontab_id
 * @property string $frequency_type
 * @property string $crontab_frequency
 * @property string $status
 * @property integer $crontab_changed
 * @property string $last_execution
 *
 * @property Reservation $id0
 */
class AutomatedTest extends \yii\db\ActiveRecord
{
	const STATUS_ENABLED = 		"ENABLED";
	const STATUS_DISABLED = 	"DISABLED";
	const STATUS_DELETED = 		"DELETED";
	const STATUS_PROCESSING = 	"PROCESSING";
	
	const FREQUENCY_DAILY =		"DAILY";
	const FREQUENCY_WEEKLY = 	"WEEKLY";
	const FREQUENCY_MONTHLY =	"MONTHLY";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%automated_test}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'frequency_type', 'crontab_frequency', 'status', 'crontab_changed'], 'required'],
            [['id'], 'integer'],
            [['frequency_type'], 'string'],
            [['last_execution'], 'safe'],
            [['crontab_id', 'crontab_frequency'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'crontab_id' => 'Crontab ID',
            'frequency_type' => Yii::t("circuits", "Frequency Type"),
            'crontab_frequency' => 'Crontab Frequency',
            'crontab_status' => Yii::t("circuits", "Status"),
            'last_execution' => Yii::t("circuits", "Last execution"),
        ];
    }
    
    public function delete() {
    	Reservation::deleteAll(['id'=>$this->id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservation()
    {
        return $this->hasOne(Reservation::className(), ['id' => 'id']);
    }
    
    public function setReservation($reservation) {
    	$this->id = $reservation->id;
    }
    
    public function getConnectionStatus() {
    	$conn = Connection::find()->where([
    			'id'=> Connection::find()->where([
    					'reservation_id'=>$this->id])->max("id")])->select(['status'])->one();
    	if ($conn) {
    		switch ($conn->status) {
    			case Connection::STATUS_PENDING:
				case Connection::STATUS_CREATED :
				case Connection::STATUS_CONFIRMED : 		return Yii::t("circuits","Testing"); 
				case Connection::STATUS_SUBMITTED : 		return Yii::t("circuits","Passed");
				case Connection::STATUS_FAILED_CREATE: 		
				case Connection::STATUS_FAILED_CONFIRM : 	
				case Connection::STATUS_FAILED_SUBMIT : 	return Yii::t("circuits","Failed");
    		}
    	} else {
    		return Yii::t("circuits","Pending");
    	}
    }
    
    public function getStatus() {
    	switch ($this->status) {
    		case self::STATUS_ENABLED: return Yii::t("circuits","Enabled");
    		case self::STATUS_DISABLED: return Yii::t("circuits","Disabled");
			case self::STATUS_PROCESSING: return Yii::t("circuits","Processing"); 
    	}
    }
    
    public function getFrequencyType() {
    	switch ($this->frequency_type) {
    		case self::FREQUENCY_DAILY: return Yii::t("circuits","Daily");
			case self::FREQUENCY_WEEKLY: return Yii::t("circuits","Weekly");
			case self::FREQUENCY_MONTHLY: return Yii::t("circuits","Monthly");
    	}
    }
    
    public function execute() {
    	if ($this->deleteIfInvalid()) {
    		return;
    	}
    	
    	$this->last_execution = date("Y-m-d H:i:s");
    	$this->save();
    	 
    	$date = new \DateTime();
    	$reservation = $this->getReservation()->one();
    	$date->modify('+10 minutes');
    	$reservation->start = $date->format('Y-m-d H:i:s');
    	$date->modify('+10 minutes');
    	$reservation->finish =  $date->format('Y-m-d H:i:s');
    	$reservation->save();
    
    	$reservation->createConnections();
    		
    	$reservation->confirm();
    }
    
    public function getSourceDomain() {
    	return $this->getSourceDevice()->one()->getNetwork()->one()->getDomain();
    }
    
    public function getSourceDevice() {
    	return $this->getSourceUrn()->one()->getDevice();
    }
    
    public function getSourceUrn() {
    	return $this->getReservation()->one()->getSourceUrn();
    }
    
    public function getDestinationDomain() {
    	return $this->getDestinationDevice()->one()->getNetwork()->one()->getDomain();
    }
    
    public function getDestinationDevice() {
    	return $this->getDestinationUrn()->one()->getDevice();
    }
    
    public function getDestinationUrn() {
    	return $this->getReservation()->one()->getDestinationUrn();
    }
    
    public function deleteIfInvalid() {
    	if(!$this->getSourceUrn() || !$this->getDestinationUrn()) {
    		$this->delete();
    		return true;
    	}
    	return false;
    }
}
