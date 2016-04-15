<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\circuits\models;

use Yii;

/**
 * This is the model class for table "{{%reservation_recurrence}}".
 *
 * @property integer $id
 * @property string $type
 * @property integer $every
 * @property string $weekdays
 * @property string $finish
 * @property integer $occurrence_limit
 *
 * @property Reservation $id0
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ReservationRecurrence extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reservation_recurrence}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'every'], 'required'],
            [['id', 'every', 'occurrence_limit'], 'integer'],
            [['type', 'weekdays'], 'string'],
            [['finish'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'every' => 'Every',
            'weekdays' => 'Weekdays',
            'finish' => 'Finish',
            'occurrence_limit' => 'Occurrence Limit',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReservation()
    {
        return $this->hasOne(Reservation::className(), ['id' => 'id']);
    }
    

    public function getEvents($firstStart, $firstFinish) {
	    //user defined event start and finish dates
	    $eventStart = new \DateTime($firstStart);
	    $eventFinish = new \DateTime($firstFinish);
	    
	    $duration = $eventStart->diff($eventFinish);
	    
	    $endRecurring = NULL;
	    if ($this->occurrence_limit)
	    	$endRecurring = $this->occurrence_limit;
	    elseif ($this->finish)
	    $endRecurring = new \DateTime($this->finish);
	    
	    //define for recurring period function
	    $begin = $eventStart;
	    $end = $endRecurring;
	     	//1. a funcao DatePeriod pede o numero de intervalos da recorrencia
	    //e esse numero eh o numero de repeticoes - 1
	    //2. lembre que every é o tamanho do intervalo entre as repeticoes.
	    
	    $interval = NULL;
	    switch ($this->type) {
	    	case "W":
	    		if (!($endRecurring && $this->every && $this->weekdays))
	    			return FALSE;
	    			else
	    				return $this->getWeeklyRecurrences($firstStart, $firstFinish);
	    			break;
	    	case "D":
	    		if (!($endRecurring && $this->every))
	    			return FALSE;
	    			$interval = new \DateInterval("P$this->every"."D");
	    			break;
	    	case "M":
	    		if (!($endRecurring && $this->every))
	    			return FALSE;
	    			$interval = new \DateInterval("P$this->every"."M");
	    			break;
	    	default:
	    		$periods = array();
	    
	    		$per = new \stdClass();
	    		$per->start = $eventStart->getTimestamp();
	    		$per->finish = $eventFinish->getTimestamp();
	    		$periods[] = $per;
	    
	    		return $periods;
	    }
	    
	    $DT_recurrences = new \DatePeriod($begin, $interval, $end);
	    
	    $periods = array();
	    if ($DT_recurrences) {
	    	foreach ($DT_recurrences as $date) {
	    
	    		if (is_int($endRecurring) && (count($periods) == $endRecurring)) {
	    			break;
	    		}
	    
	    		$temp = new \DateTime($date->format("Y-m-d H:i:s"));
	    		$start = $temp->getTimestamp();
	    
	    		$date->add($duration);
	    
	    		$temp = new \DateTime($date->format("Y-m-d H:i:s"));
	    		$finish = $temp->getTimestamp();
	    
	    		$per = new \stdClass();
	    		$per->start = $start;
	    		$per->finish = $finish;
	    		$periods[] = $per;
	    	}
	    }
	    
	    return $periods;
    }
    
    private function getWeeklyRecurrences($firstStart, $firstFinish) {
    	$temp = new \DateTime($firstStart);
    	$eventStart = $temp->getTimestamp();
    
    	$temp = new \DateTime($firstFinish);
    	$eventFinish = $temp->getTimestamp();
    
    	$count = NULL;
    	$until = NULL;
    	if ($this->occurrence_limit)
    		$count = $this->occurrence_limit;
    	elseif ($this->finish) {
    		$temp = new \DateTime($this->finish);
    		$until = $temp->getTimestamp();
    	}
    
    	$freq_timestamp = $this->getFreqTimestamp($this->type);
    	$periods = array();
    
    	$create_date = $eventStart;
    
    	$duration = $eventFinish - $eventStart;
    
    	while ( (($until) && ($eventStart <= $until)) || (($count) && (count($periods) < $count)) ) {
    		$offset = $this->every * $freq_timestamp;
    		$next_per = $this->getNext($eventStart);
    		if ($next_per) {
    			foreach ($next_per as $p) {
    				if ( ($p >= $create_date) && ( (($until) && $p <= $until) || (($count) && count($periods) < $count) ) &&
    						($p <= ($eventStart + $offset)) ) {
    							$per = new \stdClass();
    							$per->start = $p;
    							$per->finish = $p + $duration;
    							$periods[] = $per;
    						}
    			}
    		}
    		$eventStart += $offset;
    	}
    	return $periods;
    }
    
    private function getNext($begin) {
    
    	$daysleft = array();
    
    	$days = [];
    	$days = explode(",", $this->weekdays);
    
    	$dayTS = $this->getDayTimestamp();
    
    	// $days <array> : SU, MO, TU, WE, TH, FR, SA
    	foreach ($days as $d) {
    
    		// deve começar no início do período, no caso da semana: pelo domingo
    		$offset = $begin;
    		$dtemp = $this->DayofWeek($offset, TRUE);
    
    		while ($dtemp != "SU") {
    			$offset -= $dayTS;
    			$dtemp = $this->DayofWeek($offset, TRUE);
    		}
    
    		for ($i=0; $i < 7; $i++) {
    			$dw = $this->DayofWeek($offset, TRUE);
    
    			if ($d == $dw) {
    				$daysleft[] = $offset;
    				break;
    			}
    
    			$offset += $dayTS;
    		}
    	}
    
    	sort($daysleft);
    
    	return $daysleft;
    }
    
    private function getWeekTimestamp() {
    	$begin = mktime(12, 00, 00, 1, 1);
    	$end = mktime(12, 00, 00, 1, 8);
    	return ($end - $begin);
    }
    
    private function getDayTimestamp() {
    	$begin = mktime(12, 00, 00, 1, 1);
    	$end = mktime(12, 00, 00, 1, 2);
    	return ($end - $begin);
    }
    
    private function DayofWeek($date=NULL, $short=FALSE) {
    	if ($date) {
    		$dw = date("w", $date);
    	} else {
    		$dw = date("w");
    	}
    
    	switch($dw) {
    		case "0":
    			$dayweek = ($short) ? "SU" : "Sunday";
    			break;
    		case "1":
    			$dayweek = ($short) ? "MO" : "Monday";
    			break;
    		case "2":
    			$dayweek = ($short) ? "TU" : "Tuesday";
    			break;
    		case "3":
    			$dayweek = ($short) ? "WE" : "Wednesday";
    			break;
    		case "4":
    			$dayweek = ($short) ? "TH" : "Thursday";
    			break;
    		case "5":
    			$dayweek = ($short) ? "FR" : "Friday";
    			break;
    		case "6":
    			$dayweek = ($short) ? "SA" : "Saturday";
    			break;
    	}
    	return $dayweek;
    }
    
    private function getFreqTimestamp($freq) {
    	switch ($freq) {
    		case "D":
    			return $this->getDayTimestamp();
    			break;
    		case "W":
    			return $this->getWeekTimestamp();
    			break;
    		default:
    			return FALSE;
    	}
    }
}
