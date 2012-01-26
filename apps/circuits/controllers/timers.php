<?php

defined ('__MEICAN') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'apps/circuits/models/timer_info.php';
include_once 'apps/circuits/models/timer_lib.php';
include_once 'apps/circuits/controllers/reservations.php';

class timers extends Controller {

    public function add() {
        
        $start = dateTimeToDatabaseFormat(Common::POST("start_date"), Common::POST("start_time"));
        $finish = dateTimeToDatabaseFormat(Common::POST("finish_date"), Common::POST("finish_time"));

        if ($start && $finish) {

            $timer_info = new timer_info();
            $timer_info->start = $start;
            $timer_info->finish = $finish;
            
            $timer_info->freq = NULL;
            $timer_info->until = NULL;
            $timer_info->count = NULL;
            $timer_info->interval = NULL;
            $timer_info->byday = NULL;
            $timer_info->summary = NULL;
            
            // timer possui regras de recorrência
            if (Common::POST("repeat_chkbox")) {
                $timer_info->freq = Common::POST('freq'); //ok
                
                if (Common::POST('until') == "DATE") // ok
                    $timer_info->until = dateTimeToDatabaseFormat(Common::POST("until_date"), "23:59"); //ok
                elseif (Common::POST('until') == "NROCCURR")
                    $timer_info->count = Common::POST('count'); //ok
                else
                    Framework::debug("warning on add timer, missing end recurr");
                
                $timer_info->interval = Common::POST('interval'); //ok
                
                if ($timer_info->freq == "WEEKLY") {
                    $weekDays = array();
                    $htmlElems = array("sun_chkbox","mon_chkbox","tue_chkbox","wed_chkbox","thu_chkbox","fri_chkbox","sat_chkbox");
                    foreach ($htmlElems as $elem) {
                        if (Common::POST($elem)) {
                            $weekDays[] = Common::POST($elem); // ok
                        }
                    }
                    $timer_info->byday = implode(',', $weekDays);
                }
                
                $timer_info->summary = Common::POST('summary'); // ok
            }
            
            return $timer_info->insert();
        } else
            return FALSE;
    }
}
?>
