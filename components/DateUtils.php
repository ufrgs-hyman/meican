<?php

namespace meican\components;

use Yii;

class DateUtils {

	public static function toUTC($date, $time) {
		if ($date && $time) {
			$dateFormat = "d/m/Y";
			$timeFormat = "H:i";
			
			$dateTime = \DateTime::createFromFormat("$dateFormat $timeFormat", "$date $time", new \DateTimeZone(Yii::$app->formatter->timeZone));
			$dateTime->setTimezone(new \DateTimeZone("UTC"));
			return $dateTime->format("Y-m-d H:i:s");
		} else
			return null;
	}

	static function toUTCfromGMT($gmt) {
		$dateTime = new \DateTime($gmt);
		$dateTime->setTimezone(new \DateTimeZone("UTC"));
		return $dateTime->format("Y-m-d H:i:s");
	}
	
	public static function now($modify=null) {
		$date = new \DateTime('now', new \DateTimeZone("UTC"));
    	if($modify) $date->modify($modify);
    	$now = $date->format('Y-m-d H:i:s');
    	return $now;
	}
	
	public static function serverTime() {
		$date = new \DateTime('now', new \DateTimeZone("America/Sao_Paulo"));
		$now = $date->format('Y-m-d H:i:s');
		return $now;
	}
}

?>