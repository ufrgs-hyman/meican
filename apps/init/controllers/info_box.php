<?php

defined('__MEICAN') or die("Invalid access.");

include_once 'libs/controller.php';
include_once 'libs/auth.php';
include_once 'libs/meican_mail.php';
include_once 'apps/aaa/models/user_info.php';

class info_box extends Controller {

    public function info_box() {
        $this->app = 'init';
        $this->controller = 'info_box';
        $this->defaultAction = 'show';
        $this->setLayout('info_box');
    }

    public function show() {
        $args = new stdClass();
        $args->usr_login = AuthSystem::getUserLogin();
        $args->system_time = date("d/m/Y H:i");

        $this->setArgsToBody($args);
        $this->render();
    }

    public function time() {
        $this->setLayout('empty');
        $this->action = 'time';
        $this->render();
    }

    public function feedback_submit() {
        $email = new Meican_Mail();

        $user = new user_info();
        $user->usr_id = AuthSystem::getUserId();
        $ret_usr = $user->fetch();

        $body = "User: " . $ret_usr[0]->usr_name . "\n";
        $body .= ($ret_usr[0]->usr_email) ? "E-mail: " . $ret_usr[0]->usr_email . "\n\n" : "\n";

        $topic = Common::POST('topic');

        $body .= "Type: " . $topic['style'] . "\n\n";

        $body .= "Title: " . $topic['subject'] . "\n";
        $body .= "Message: " . $topic['additional_detail'] . "\n";

        $body .= "\n";
        $body .= "Makes me feel: " . $topic['emotitag']['feeling'] . "\n";

//        ob_start();
//        print_r($_POST);
//        $body = ob_get_contents();
//        ob_end_clean();
        Framework::debug("e-mail fb", $body);
        $to = "luis.armandob@gmail.com, felipenesello@gmail.com, lfaganello@gmail.com";
        if ($email->send($to, $body, "Feedback from MEICAN"))
            echo _("Feedback sent") . ". " . ("Thank you") . "!";
        else
            echo _("Error sending feedback") . ". " . ("Try again later") . ".";
    }

}

?>
