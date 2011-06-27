<?php

class timer_info extends Model {

    public function timer_info() {
        $this->setTableName("timer_info");

        // Add all table attributes
        $this->addAttribute("tmr_id", "INTEGER", TRUE, FALSE, FALSE);
        $this->addAttribute("tmr_name", "VARCHAR");
        $this->addAttribute("start", "VARCHAR");
        $this->addAttribute("finish", "VARCHAR");
        $this->addAttribute("freq", "VARCHAR");
        $this->addAttribute("until", "VARCHAR");
        $this->addAttribute("count", "INTEGER");
        $this->addAttribute("interval", "INTEGER");
        $this->addAttribute("byday", "VARCHAR");
        $this->addAttribute("summary", "VARCHAR");
    }

    public function getRecurrences() {

        //user defined event start and finish dates
        $eventStart = new DateTime($this->start);
        $eventFinish = new DateTime($this->finish);

        $duration = $eventStart->diff($eventFinish);

        $endRecurring = NULL;
        if ($this->count)
            $endRecurring = $this->count;
        elseif ($this->until)
            $endRecurring = new DateTime($this->until);

        if (!$endRecurring)
            return FALSE;

        //define for recurring period function
        $begin = $eventStart;
        $end = $endRecurring;

        $interval = array();

        if ($this->freq == "WEEKLY") {
            return getRecurrences($this);

            $weekdays = explode(",", $this->byday);

            if ($weekdays) {
                foreach ($weekdays as $d) {
                    switch ($d) {
                        case "SU":
                            $weekday = "sunday";
                            break;
                        case "MO":
                            $weekday = "monday";
                            break;
                        case "TU":
                            $weekday = "tuesday";
                            break;
                        case "WE":
                            $weekday = "wednesday";
                            break;
                        case "TH":
                            $weekday = "thursday";
                            break;
                        case "FR":
                            $weekday = "friday";
                            break;
                        case "SA":
                            $weekday = "saturday";
                            break;
                        default:
                            $weekday = NULL;
                            break;
                    }

                    if ($weekday) {
                        if ($this->interval > 1) {
                            for ($i=0; $i<10; $i++)
                                $interval[] = DateInterval::createFromDateString("next $weekday +".($i*$this->interval)." weeks");
                        } else
                            $interval[] = DateInterval::createFromDateString("next $weekday");
                    }
                }
            } else
                return FALSE;
        } elseif ($this->freq == "DAILY") {
            $interval[] = new DateInterval("P$this->interval"."D");
        } elseif ($this->freq == "MONTHLY") {
            $interval[] = new DateInterval("P$this->interval"."M");
        } else
            return FALSE;

        $periods = array();
        foreach ($interval as $i)
            $periods[] = new DatePeriod($begin, $i, $end, DatePeriod::EXCLUDE_START_DATE);

        // preenche o array de DateTime de acordo com as recorrências geradas
        $DateTimeArray = array();
        $DateTimeArray[] = $eventStart;
        foreach ($periods as $perArray) {
            foreach ($perArray as $p) {
                $DateTimeArray[] = $p;
            }
        }

        // realiza o ordenamento das datas
        sort($DateTimeArray);

        /**
         * <array><DateTime> $temp
         */
        $periods = array();
        foreach ($DateTimeArray as $date) {
            /**
             * <DateTime> $date
             * <DateInterval> $duration
             */

            if (($this->count) && (count($periods) == $this->count)) {
                break;
            }

            //$start = $p->format("Y-m-d H:i:s");
            $temp = new DateTime($date->format("Y-m-d H:i:s"));
            $start = $temp->getTimestamp();

            $date->add($duration);

            //$finish = $p->format("Y-m-d H:i:s");
            $temp = new DateTime($date->format("Y-m-d H:i:s"));
            $finish = $temp->getTimestamp();

            unset($per);
            $per->start = $start;
            $per->finish = $finish;
            $periods[] = $per;
        }

        return $periods;
    }

}

function getMonthTimestamp($date){
    $month = date('m',$date);
    $year =  date('Y',$date);
    //CUIDAR HORARIO DE VERAO
    $begin = mktime(12, 00, 00, $month, 1, $year, false);
    $end = mktime(12, 00, 00, $month+1, 1, $year, false);
    return ($end - $begin);
}

function getWeekTimestamp() {
    $begin = mktime(12, 00, 00, 1, 1);
    $end = mktime(12, 00, 00, 1, 8);
    return ($end - $begin);
}

function getDayTimestamp() {
    $begin = mktime(12, 00, 00, 1, 1);
    $end = mktime(12, 00, 00, 1, 2);
    return ($end - $begin);
}

function getTimestamp($freq, $date) {
    switch ($freq) {
        case "Daily": return getDayTimestamp(); break;
        case "WEEKLY": return getWeekTimestamp(); break;
        case "Monthly": return getMonthTimestamp($date); break;
    }
}

function DayofWeek($date) {

	$dw = date("w", $date);

	switch($dw) {
		case"0": $dayweek = "SU";  break;
		case"1": $dayweek = "MO";  break;
		case"2": $dayweek = "TU";  break;
		case"3": $dayweek = "WE";  break;
		case"4": $dayweek = "TH";  break;
		case"5": $dayweek = "FR";  break;
		case"6": $dayweek = "SA";  break;
	}
        return $dayweek;
}

function getNext($data_inicio, $byday, $freq) {

    $daysleft = array();

    if ($freq == "WEEKLY") {
        $days = array();
        $days = explode(',',$byday);

        $dayTS = getDayTimestamp();

        // $days <array> : SU, MO, TU, WE, TH, FR, SA
        foreach ($days as $d) {

            // deve começar no início do período, no caso da semana: pelo domingo
            $offset = $data_inicio;
            $dtemp = DayofWeek($offset);

            while ($dtemp != "SU") {
                $offset -= $dayTS;
                $dtemp = DayofWeek($offset);
            }

            for ($i=0; $i < 7; $i++) {

                //incremento tá ok 86400 em 86400
                $dw = DayofWeek($offset);

                if ($d == $dw) {
                    $daysleft[] = $offset;
                    break;
                }

                $offset += $dayTS;
            }
        }
        //Framework::debug('Dias', date('d/m/Y',$daysleft));
        sort($daysleft);

    } else
        $daysleft[] = $data_inicio;

    return $daysleft;
}

function getRecurrences($timer) {

    $count = NULL;
    $until = NULL;
    if ($timer->count)
        $count = $timer->count;
    elseif ($timer->until) {
        $temp = new DateTime($timer->until);
        $until = $temp->getTimestamp();
    }

    if (!($count || $until))
        return FALSE;

    //$dtstart = $timer->start;
    //$dtend = $timer->end;
    $interval = $timer->interval;
    $byday = $timer->byday;
    $freq = $timer->freq;

    $temp = new DateTime($timer->start);
    $data_inicio = $temp->getTimestamp();

    $temp = new DateTime($timer->finish);
    $data_fim = $temp->getTimestamp();

    $freq_timestamp = getTimestamp($freq, $data_inicio);
    $periods = array();

    $create_date = $data_inicio;

    $duration = $data_fim - $data_inicio;

    while ( (($until) && ($data_inicio <= $until)) || (($count) && (count($periods) < $count)) ) {
        $offset = $interval * getTimeStamp($freq,$data_inicio);
        $next_per = getNext($data_inicio, $byday, $freq);
        if ($next_per) {
            foreach ($next_per as $p) {
                if ( ($p >= $create_date) && ( (($until) && $p <= $until) || (($count) && count($periods) < $count) ) && ($p <= ($data_inicio + $offset)) ) {
                    unset($per);
                    $per->start = $p;
                    $per->finish = $p + $duration;
                    $periods[] = $per;
                }
            }
        }
        $data_inicio += $offset;
    }
    return $periods;
}

?>
