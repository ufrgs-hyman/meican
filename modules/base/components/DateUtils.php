<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\components;

use Yii;

/**
 * @author Maurício Quatrin Guerreiro
 */
class DateUtils {

    public static function fromDB($datetime) {
        return new \DateTime($datetime, new \DateTimeZone("UTC"));
    }

    public static function fromLocal($datetime, $format = "d/m/Y H:i") {
        $dateTime = \DateTime::createFromFormat($format, $datetime, new \DateTimeZone(Yii::$app->formatter->timeZone));
        $dateTime->setTimezone(new \DateTimeZone("UTC"));
        return $dateTime;
    }

    public static function localToUTC($datetime, $format = "d/m/Y H:i") {
        if ($datetime) {
            $dateTime = \DateTime::createFromFormat($format, $datetime, new \DateTimeZone(Yii::$app->formatter->timeZone));
            $dateTime->setTimezone(new \DateTimeZone("UTC"));
            return $dateTime->format("Y-m-d H:i:s");
        } else
            return null;
    }

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