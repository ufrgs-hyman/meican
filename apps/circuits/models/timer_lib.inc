<?php

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

function DayofWeek($date=NULL, $short=FALSE) {

    $dw = date("w", $date);

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

function getFreqTimestamp($freq) {
    switch ($freq) {
        case "DAILY":
            return getDayTimestamp();
            break;
        case "WEEKLY":
            return getWeekTimestamp();
            break;
        default:
            return FALSE;
    }
}

?>