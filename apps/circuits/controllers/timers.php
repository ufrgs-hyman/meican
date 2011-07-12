<?php

defined ('__FRAMEWORK') or die ("Invalid access.");

include_once 'libs/controller.php';
include_once 'apps/circuits/models/timer_info.inc';
include_once 'apps/circuits/models/timer_lib.inc';
include_once 'apps/circuits/controllers/reservations.php';

class timers extends Controller {

    public function timers() {
        $this->app = 'circuits';
        $this->controller = 'timers';
        $this->defaultAction = 'show';
    }

    public function dummy() {
        _("hours");
        _("hour");
        _("minutes");
        _("minute");
        _("days");
        _("day");
    }

    public function show() {
        // destrói variáveis, caso clicou em timers antes de passar por reservations
        Common::destroySessionVariable('res_name');
        Common::destroySessionVariable('sel_flow');
        Common::destroySessionVariable('sel_timer');
        Common::destroySessionVariable('res_wizard');

        $timer_info = new timer_info();
        $allTimers = $timer_info->fetch();

        if ($allTimers) {
            $timers = array();

            foreach ($allTimers as $t) {
                $tim = new timer_info();
                $tim->tmr_id = $t->tmr_id;
                $timer = $tim->getTimerDetails();

                $timer->editable = TRUE;
                $timer->deletable = TRUE;
                $timer->selectable = FALSE;

                $timers[] = $timer;
            }
            $this->setAction('show');

            $this->setArgsToBody($timers);
        } else {
            $this->setAction('empty');

            $args = new stdClass();
            $args->title = _("Timers");
            $args->message = _("You have no timer, click the button below to create a new one");
            $this->setArgsToBody($args);
        }

        $this->render();
    }

    public function add_form() {
        $dateFormat = "d/m/Y";
        $js_dateFormat = "dd/mm/yy";
        //$dateFormat = "M j, Y";
        //$js_dateFormat = "M d, yy";

        $hourFormat = "H:i";
        //$hourFormat = "g:i a";

        $hoursArray = array();
        for ($h=0; $h < 24; $h++) {
            for ($m=0; $m < 60; $m=$m+5) {
                $hour = ($h < 10) ? "0$h" : $h;
                $min = ($m < 10) ? "0$m" : $m;
                $hoursArray[] = "$hour:$min";
            }
        }

        $today_check = DayofWeek();

        // @lang : pt_BR.utf8
        $lang = explode(".", Language::getLang());
        $js_lang = str_replace("_", "-", $lang[0]);
        
        // @js_lang : pt-BR

        $this->setArgsToScript(array(
                "date_format" => $js_dateFormat,
                "language" => $js_lang,
                "horas" => $hoursArray,
                "today" => $today_check,
                "repeat_every_string" => _("Repeat every"),
                "day_string" => _("day"),
                "days_string" => _("days"),
                "week_string" => _("week"),
                "weeks_string" => _("weeks"),
                "on_string" => _("on"),
                "month_string" => _("month"),
                "months_string" => _("months"),
                "until_string" => _("until"),
                "times_string" => _("times"),
                "time_string" => _("time"),
                "end_rule_string" => _("Please set an end rule"),
                "select_day_string" => _("Select at least one day"),
                "set_name_string" => _("Set name"),
                "invalid_time_string" => _("Invalid time")
                )
        );

        $this->addScript('timers');

        $this->addScript("jquery.ui.datepicker-$js_lang");

        $this->setInlineScript('timers_add');

        $this->setAction("add");

        $args = new stdClass();
        $args->start_date = date($dateFormat);
        $args->finish_date = date($dateFormat);
        $args->start_time = date($hourFormat, (time()+5*60));
        $args->finish_time = date($hourFormat, (time()+10*60));
        $args->res_wizard = (Common::hasSessionVariable('res_wizard')) ? TRUE : FALSE;

        $this->setArgsToBody($args);
        $this->render();
    }

    public function update() {
        // se estiver editando, tem tmr_id
        // se estiver adicionando, tmr_id é nulo
        if (Common::POST('tmr_id'))
            $this->modify();
        else
            $this->add();
    }

    private function add() {
        $tmr_name = Common::POST('name');
        
        $start = dateTimeToDatabaseFormat(Common::POST("start_date"), Common::POST("start_time"));
        $finish = dateTimeToDatabaseFormat(Common::POST("finish_date"), Common::POST("finish_time"));

        if ($tmr_name && $start && $finish) {

            $timer_info = new timer_info();
            $timer_info->tmr_name = $tmr_name;
            $timer_info->start = $start;
            $timer_info->finish = $finish;

            $freq = Common::POST('freq');

            // timer possui regras de recorrência
            if ($freq) {
                $timer_info->freq = $freq;
                $timer_info->until = dateTimeToDatabaseFormat(Common::POST("until_date"), "23:59");
                $timer_info->count = Common::POST('count');
                $timer_info->interval = Common::POST('interval');
                $timer_info->byday = Common::POST('byday');
                $timer_info->summary = Common::POST('summary');
            }

            if ($result = $timer_info->insert()) {
                if (Common::hasSessionVariable('res_wizard')) {
                    $res = new reservations();
                    $res->update_timer($result->tmr_id);
                    $res->setFlash(_("Timer")." '$timer_info->tmr_name' "._("added"), "success");
                    $res->page2();
                } else {
                    $this->setFlash(_("Timer")." '$timer_info->tmr_name' "._("added"), "success");
                    $this->show();
                }
                return;
            } else
                $this->setFlash(_("Fail to create timer"), "error");

        } else
            $this->setFlash(_("Missing arguments"), "error");

        $this->add_form();
    }

    private function modify() {
        $tmr_name = Common::POST('name');
        
        $start = dateTimeToDatabaseFormat(Common::POST("start_date"), Common::POST("start_time"));
        $finish = dateTimeToDatabaseFormat(Common::POST("finish_date"), Common::POST("finish_time"));

        if ($tmr_name && $start && $finish) {

            $timer_info = new timer_info();
            $timer_info->tmr_id = Common::POST('tmr_id');
            $timer_info->tmr_name = $tmr_name;
            $timer_info->start = $start;
            $timer_info->finish = $finish;

            $freq = Common::POST('freq');

            // timer possui regras de recorrência
            if ($freq) {
                $timer_info->freq = $freq;

                $until = dateTimeToDatabaseFormat(Common::POST("until_date"), "23:59");
                $count = Common::POST('count');
                if ($until) {
                    $timer_info->until = $until;
                    $timer_info->count = "NULL";
                } elseif ($count) {
                    $timer_info->until = "0000-00-00 00:00:00";
                    $timer_info->count = $count;
                }

                $timer_info->interval = Common::POST('interval');
                $timer_info->byday = Common::POST('byday');
                $timer_info->summary = Common::POST('summary');
            } else {
                $timer_info->freq = "NULL";
                $timer_info->until = "NULL";
                $timer_info->count = "NULL";
                $timer_info->interval = "NULL";
                $timer_info->byday = "NULL";
                $timer_info->summary = "NULL";
            }

            if ($timer_info->update()) {
                if (Common::hasSessionVariable('res_wizard')) {
                    $res = new reservations();
                    $res->update_timer($timer_info->tmr_id);
                    $res->setFlash(_("Timer")." '$timer_info->tmr_name' "._("updated"), "success");
                    $res->page2();
                } else {
                    $this->setFlash(_("Timer")." '$timer_info->tmr_name' "._("updated"), "success");
                    $this->show();
                }
                return;
            } else
                $this->setFlash(_("No change has been made"), "warning");

        } else
            $this->setFlash(_("Missing arguments"), "error");

        $this->edit(array("tmr_id" => $timer->tmr_id));
    }

    public function edit($timer_id_array) {
        if (array_key_exists('tmr_id', $timer_id_array)) {
            $timerId = $timer_id_array['tmr_id'];
        } else {
            $this->setFlash(_("Invalid index"), "fatal");
            $this->show();
            return;
        }

        $timer_info = new timer_info();
        $timer_info->tmr_id = $timerId;
        $result = $timer_info->fetch();

        $timer = NULL;
        if ($result === FALSE) {
            $this->setFlash(_("Timer not found"), "fatal");
            $this->show();
            return;
        } else {
            $timer = $result[0];
        }

        $dateFormat = "d/m/Y";
        $js_dateFormat = "dd/mm/yy";
        //$dateFormat = "M j, Y";
        //$js_dateFormat = "M d, yy";

        $hourFormat = "H:i";
        //$hourFormat = "g:i a";

        $hoursArray = array();
        for ($h=0; $h < 24; $h++) {
            for ($m=0; $m < 60; $m=$m+5) {
                $hour = ($h < 10) ? "0$h" : $h;
                $min = ($m < 10) ? "0$m" : $m;
                $hoursArray[] = "$hour:$min";
            }
        }

        $today_check = DayofWeek();

        $lang = explode(".", Language::getLang());
        $js_lang = str_replace("_", "-", $lang[0]);

        $start = new DateTime($timer->start);
        $finish = new DateTime($timer->finish);

        $start_date = $start->format($dateFormat);
        $finish_date = $finish->format($dateFormat);

        $start_time = $start->format($hourFormat);
        $finish_time = $finish->format($hourFormat);

        if ($timer->freq) {
            $recurrence = new stdClass();
            $recurrence->freq = $timer->freq;
            $recurrence->interval = $timer->interval;
            if ($timer->count) {
                $recurrence->count = $timer->count;
                $recurrence->until = NULL;
                $timer->until = NULL;
            } elseif ($timer->until) {
                $until = new DateTime($timer->until);
                $recurrence->until = $until->format($dateFormat);
                $timer->until = $recurrence->until;
                $recurrence->count = NULL;
                $timer->count = NULL;
            }
            if ($timer->freq == "WEEKLY") {
                $recurrence->byday = $timer->byday;
            } else
                $recurrence->byday = NULL;
        } else
            $recurrence = "";

        $this->setArgsToScript(array(
                "recurrence" => $recurrence,
                "date_format" => $js_dateFormat,
                "language" => $js_lang,
                "horas" => $hoursArray,
                "today" => $today_check,
                "repeat_every_string" => _("Repeat every"),
                "day_string" => _("day"),
                "days_string" => _("days"),
                "week_string" => _("week"),
                "weeks_string" => _("weeks"),
                "on_string" => _("on"),
                "month_string" => _("month"),
                "months_string" => _("months"),
                "until_string" => _("until"),
                "times_string" => _("times"),
                "time_string" => _("time"),
                "end_rule_string" => _("Please set an end rule"),
                "select_day_string" => _("Select at least one day"),
                "set_name_string" => _("Set name"),
                "invalid_time_string" => _("Invalid time")
        ));

        $args = new stdClass();
        $args->start_date = $start_date;
        $args->finish_date = $finish_date;
        $args->start_time = $start_time;
        $args->finish_time = $finish_time;
        $args->timer = $timer;
        $args->res_wizard = (Common::hasSessionVariable('res_wizard')) ? TRUE : FALSE;

        $this->setArgsToBody($args);

        $this->addScript("timers");
        $this->setInlineScript("timers_edit");

        $this->setAction("edit");
        $this->render();
    }

    public function delete() {
        $del_timers = Common::POST("del_checkbox");
        if ($del_timers) {
            foreach($del_timers as $timerId) {
                $timer = new timer_info();
                $timer->tmr_id = $timerId;
                $tmp = $timer->fetch();
                $result = $tmp[0];
                if ($timer->delete())
                    $this->setFlash(_("Timer")." '$result->tmr_name' "._("deleted"), 'success');
            }
        }
        $this->show();
    }

}

?>
