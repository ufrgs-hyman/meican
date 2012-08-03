<?php

class Common {

    static function getSessionVariable($variable) {
        if (defined('NO_SESSION'))
            return false;
        if (!session_id()) {
            session_start();
        }
        return (isset($_SESSION [$variable]) ? $_SESSION [$variable] : "");
    }

    static function setSessionVariable($variable, $value) {
        if (!session_id()) {
            session_start();
        }
        $_SESSION [$variable] = $value;
        return $_SESSION [$variable];
    }

    static function destroySessionVariable($variable) {
        if (!session_id()) {
            session_start();
        }
        unset($_SESSION [$variable]);
    }

    static function hasSessionVariable($variable) {
        if (!session_id()) {
            session_start();
        }
        return isset($_SESSION [$variable]);
    }

    static function rescueVar($var) {
        $result = FALSE;

        $result = Common::getSessionVariable($var);

        return $result;
    }

    static function recordVar($var, $value) {

        Common::setSessionVariable($var, $value);
    }

    static function mysql_replace($inp) {
        if (is_array($inp))
            return array_map(__METHOD__, $inp);

        if (!empty($inp) && is_string($inp)) {
            $inp = trim($inp);
            $return = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp, &$count);
            if ($count > 0)
                debug("trying sql injection");
            return htmlspecialchars($return);
        }

        return $inp;
    }

    static function GET($var) {
        if (array_key_exists($var, $_GET))
            if (!empty($_GET[$var])) {
                $get = trim($_GET[$var]);
                return self::mysql_replace($get);
            }
        return FALSE;
    }

    static function POST($var) {
        // variáveis que estão setadas, ou seja, o campo existe no formulário do HTML
        if (array_key_exists($var, $_POST)) {
            // variáveis que possuem conteúdo, pode ser zero
            if ($_POST[$var] != "") {
                $post = (is_array($_POST[$var]) || is_object($_POST[$var])) ? $_POST[$var] : trim($_POST[$var]);
                return self::mysql_replace($post);
            }
        }
        // variável não existe ou em branco
        return NULL;
    }

    static function apc_update() {
        $now = time();
        apc_store('last_update', $now);
    }

    static function getLastUpdate() {
        date_default_timezone_set("America/Sao_Paulo");
        $now = time();
        $last_update_server = apc_fetch('last_update');
        if ($last_update_server) {
            $timepout_1_week = 604800;
            if (($now - $last_update_server) > $timepout_1_week) {
                apc_store('last_update', $now);
                return $now;
            } else
                return $last_update_server;
        } else { //nao encontrou nos servidor. inicializacao da variavel
            apc_store('last_update', $now);
            return $now;
        }
    }

    /**
     *
     * @param Array $array An array of objects
     * @param String $attribute An attribute name to extract to a single array
     * @return Array A single array containing only the attribute specified
     */
    static function arrayExtractAttr($array, $attribute) {
        $extractedArray = array();
        if (is_array($array) && !empty($attribute)) {
            foreach ($array as $a) {
                if (array_key_exists($attribute, $a))
                    $extractedArray[] = $a->{$attribute};
            }
        }
        return $extractedArray;
    }

}

class DATABASE_CONFIG {

    function __construct() {
        $configs = Configure::read('databases');
        foreach ($configs as $k => $v)
            $this->{$k} = $v;
    }

}
