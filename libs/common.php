<?php

include_once 'libs/cookies.php';
include_once 'libs/database.php';

class Common {

    static function getSessionVariable($variable) {
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

//        if (!$result)
//            $result = CookiesSystem::getCookie($var);

        return $result;
    }

    static function recordVar($var, $value) {

        Common::setSessionVariable($var, $value);
        //CookiesSystem::setCookie($var, $value);
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
        return NULL;
    }

    static function apc_update() {
        $now = mktime();
        apc_store('last_update', $now);
    }

    static function getLastUpdate(){
        date_default_timezone_set("America/Sao_Paulo");
        $now = mktime();
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

}

/**
 * Prints out debug information about given variable.
 *
 * Only runs if debug level is greater than zero.
 *
 * @param boolean $var Variable to show debug information for.
 * @param boolean $showHtml If set to true, the method prints the debug data in a screen-friendly way.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @link http://book.cakephp.org/view/1190/Basic-Debugging
 * @link http://book.cakephp.org/view/1128/debug
 */
function debug($var = false, $showHtml = false, $showFrom = true) {
    if (Framework::$debugMode > 1) {
        if ($showFrom) {
            $calledFrom = debug_backtrace();
            echo '<strong>' . substr(str_replace('', '', $calledFrom[0]['file']), 1) . '</strong>';
            echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
        }
        echo "\n<pre class=\"cake-debug\">\n";

        $var = print_r($var, true);
        if ($showHtml) {
            $var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
        }
        echo $var . "\n</pre>\n";
    }
    
}

?>