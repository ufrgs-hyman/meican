<?php

include_once 'libs/resource_model.inc';
include_once 'apps/circuits/models/timer_lib.inc';

class timer_info extends Resource_Model {

    public function timer_info() {
        $this->setTableName("timer_info");

        // Add all table attributes
        $this->addAttribute("tmr_id", "INTEGER", TRUE, FALSE, FALSE);
        
        $this->addAttribute("start", "VARCHAR");
        $this->addAttribute("finish", "VARCHAR");
        
        $this->addAttribute("freq", "VARCHAR");
        $this->addAttribute("until", "VARCHAR");
        $this->addAttribute("count", "INTEGER");
        $this->addAttribute("interval", "INTEGER");
        $this->addAttribute("byday", "VARCHAR");
        $this->addAttribute("summary", "VARCHAR");
    }

    public function getTimerDetails() {
        if (!isset ($this->tmr_id)) {
            return FALSE;
        } else {
            $ret = $this->fetch();
            if (!$ret) {
                return FALSE;
            }
            $timer = $ret[0];

            $dateFormat = "d/m/Y";
            //$dateFormat = "M j, Y";

            $hourFormat = "H:i";
            //$hourFormat = "g:i a";

            $timerData = new stdClass();

            $timerData->id = $timer->tmr_id;
            $timerData->name = $timer->tmr_name;

            $start = new DateTime($timer->start);
            $finish = new DateTime($timer->finish);

            $timerData->start = $start->format("$dateFormat $hourFormat");
            $timerData->finish = $finish->format("$dateFormat $hourFormat");

            $duration = $start->diff($finish);

            $dur_array = array();

            if ($duration->format('%d')) {
                $temp = $duration->format('%d ');
                $temp .= ($duration->format('%d') > 1) ? _("days") : _("day");
                $dur_array[] = $temp;
            }
            if ($duration->format('%h')) {
                $temp = $duration->format('%h ');
                $temp .= ($duration->format('%h') > 1) ? _("hours") : _("hour");
                $dur_array[] = $temp;
            }
            if ($duration->format('%i')) {
                $temp = $duration->format('%i ');
                $temp .= ($duration->format('%i') > 1) ? _("minutes") : _("minute");
                $dur_array[] = $temp;
            }

            $timerData->duration = ($dur_array) ? implode(", ", $dur_array) : "0 "._("minute");

            $timerData->summary = ($timer->freq && $timer->summary) ? $timer->summary : "-";

            return $timerData;
        }
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

        //define for recurring period function
        $begin = $eventStart;
        $end = $endRecurring;

        $interval = NULL;
        switch ($this->freq) {
            case "WEEKLY":
                if (!($endRecurring && $this->interval && $this->byday))
                    return FALSE;
                else
                    return $this->getWeeklyRecurrences();
                break;
            case "DAILY":
                if (!($endRecurring && $this->interval))
                    return FALSE;
                $interval = new DateInterval("P$this->interval"."D");
                break;
            case "MONTHLY":
                if (!($endRecurring && $this->interval))
                    return FALSE;
                $interval = new DateInterval("P$this->interval"."M");
                break;
            default:
                $periods = array();

                $per = new stdClass();
                $per->start = $eventStart->getTimestamp();
                $per->finish = $eventFinish->getTimestamp();
                $periods[] = $per;
                
                return $periods;
        }

        $DT_recurrences = new DatePeriod($begin, $interval, $end);

        /**
         * <array><DateTime> $DT_recurrences
         */
        $periods = array();
        if ($DT_recurrences) {
            foreach ($DT_recurrences as $date) {
                /**
                 * <DateTime> $date
                 * <DateInterval> $duration
                 */

                if (is_int($endRecurring) && (count($periods) == $endRecurring)) {
                    break;
                }

                $temp = new DateTime($date->format("Y-m-d H:i:s"));
                $start = $temp->getTimestamp();

                $date->add($duration);

                $temp = new DateTime($date->format("Y-m-d H:i:s"));
                $finish = $temp->getTimestamp();

                $per = new stdClass();
                $per->start = $start;
                $per->finish = $finish;
                $periods[] = $per;
            }
        }

        return $periods;
    }

    private function getWeeklyRecurrences() {
        $temp = new DateTime($this->start);
        $eventStart = $temp->getTimestamp();

        $temp = new DateTime($this->finish);
        $eventFinish = $temp->getTimestamp();

        $count = NULL;
        $until = NULL;
        if ($this->count)
            $count = $this->count;
        elseif ($this->until) {
            $temp = new DateTime($this->until);
            $until = $temp->getTimestamp();
        }

        $freq_timestamp = getFreqTimestamp($this->freq);
        $periods = array();

        $create_date = $eventStart;

        $duration = $eventFinish - $eventStart;

        while ( (($until) && ($eventStart <= $until)) || (($count) && (count($periods) < $count)) ) {
            $offset = $this->interval * $freq_timestamp;
            $next_per = $this->getNext($eventStart);
            if ($next_per) {
                foreach ($next_per as $p) {
                    if ( ($p >= $create_date) && ( (($until) && $p <= $until) || (($count) && count($periods) < $count) ) && ($p <= ($eventStart + $offset)) ) {
                        unset($per);
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

        $days = array();
        $days = explode(',', $this->byday);

        $dayTS = getDayTimestamp();

        // $days <array> : SU, MO, TU, WE, TH, FR, SA
        foreach ($days as $d) {

            // deve começar no início do período, no caso da semana: pelo domingo
            $offset = $begin;
            $dtemp = DayofWeek($offset, TRUE);

            while ($dtemp != "SU") {
                $offset -= $dayTS;
                $dtemp = DayofWeek($offset, TRUE);
            }

            for ($i=0; $i < 7; $i++) {
                $dw = DayofWeek($offset, TRUE);

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

}

?>
