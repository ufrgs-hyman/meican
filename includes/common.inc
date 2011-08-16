<?php
include_once 'includes/cookies.inc';
include_once 'libs/database.inc';

class Common {

    static function getSessionVariable ($variable) {
        if(!session_id()) {
            session_start();
        }
        return (isset ($_SESSION [$variable]) ? $_SESSION [$variable] : "");
    }

    static function setSessionVariable ($variable, $value) {
        if(!session_id()) {
            session_start();
        }
        $_SESSION [$variable] = $value;
        return $_SESSION [$variable];
    }

    static function destroySessionVariable ($variable) {
        if(!session_id()) {
            session_start();
        }
        unset ($_SESSION [$variable]);
    }

    static function hasSessionVariable ($variable) {
        if(!session_id()) {
            session_start();
        }
        return isset ($_SESSION [$variable]);
    }

    static function rescueVar($var) {
        $result = FALSE;

        $result = Common::getSessionVariable($var);

        if (!$result)
            $result = CookiesSystem::getCookie($var);

        return $result;
    }

    static function recordVar($var, $value) {

        Common::setSessionVariable($var, $value);
        CookiesSystem::setCookie($var, $value);
    }


    static function GET($var) {
        if (array_key_exists($var, $_GET))
            if (!empty($_GET[$var])) {
                $get = trim($_GET[$var]);
                return Database::mysql_replace($get);
            }
        return FALSE;
    }

    static function POST($var) {
        // variáveis que estão setadas, ou seja, o campo existe no formulário do HTML
        if (array_key_exists($var, $_POST)) {
            // variáveis que possuem conteúdo, pode ser zero
            if ($_POST[$var] != "") {
                $post = (is_array($_POST[$var]) || is_object($_POST[$var])) ? $_POST[$var] : trim($_POST[$var]);
                return Database::mysql_replace($post);
            }
        }
        // variável não existe ou em branco
        return FALSE;
    }

    static function apc_update(){
        $now = mktime();
        apc_store('last_update',$now);
    }


    static function getLastUpdate(){
        date_default_timezone_set("America/Sao_Paulo");
        $now = mktime();
        $last_update_server = apc_fetch('last_update');
        if ($last_update_server) {
            $timepout_1_week = 604800;
            if (($now - $last_update_server) > $timepout_1_week){
                apc_store('last_update', $now);
                return $now;
            } else return $last_update_server;
        } else { //nao encontrou nos servidor. inicializacao da variavel
             apc_store('last_update', $now);
             return $now;
        }
    }
}

?>