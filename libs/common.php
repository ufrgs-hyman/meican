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

    static function getLastUpdate() {
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
    if (Configure::read('debug') > 1) {
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

/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#env
 */
function env($key) {
	if ($key === 'HTTPS') {
		if (isset($_SERVER['HTTPS'])) {
			return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		}
		return (strpos(env('SCRIPT_URI'), 'https://') === 0);
	}

	if ($key === 'SCRIPT_NAME') {
		if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
			$key = 'SCRIPT_URL';
		}
	}

	$val = null;
	if (isset($_SERVER[$key])) {
		$val = $_SERVER[$key];
	} elseif (isset($_ENV[$key])) {
		$val = $_ENV[$key];
	} elseif (getenv($key) !== false) {
		$val = getenv($key);
	}

	if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
		$addr = env('HTTP_PC_REMOTE_ADDR');
		if ($addr !== null) {
			$val = $addr;
		}
	}

	if ($val !== null) {
		return $val;
	}

	switch ($key) {
		case 'SCRIPT_FILENAME':
			if (defined('SERVER_IIS') && SERVER_IIS === true) {
				return str_replace('\\\\', '\\', env('PATH_TRANSLATED'));
			}
			break;
		case 'DOCUMENT_ROOT':
			$name = env('SCRIPT_NAME');
			$filename = env('SCRIPT_FILENAME');
			$offset = 0;
			if (!strpos($name, '.php')) {
				$offset = 4;
			}
			return substr($filename, 0, strlen($filename) - (strlen($name) + $offset));
			break;
		case 'PHP_SELF':
			return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
			break;
		case 'CGI_MODE':
			return (PHP_SAPI === 'cgi');
			break;
		case 'HTTP_BASE':
			$host = env('HTTP_HOST');
			$parts = explode('.', $host);
			$count = count($parts);

			if ($count === 1) {
				return '.' . $host;
			} elseif ($count === 2) {
				return '.' . $host;
			} elseif ($count === 3) {
				$gTLD = array(
					'aero',
					'asia',
					'biz',
					'cat',
					'com',
					'coop',
					'edu',
					'gov',
					'info',
					'int',
					'jobs',
					'mil',
					'mobi',
					'museum',
					'name',
					'net',
					'org',
					'pro',
					'tel',
					'travel',
					'xxx'
				);
				if (in_array($parts[1], $gTLD)) {
					return '.' . $host;
				}
			}
			array_shift($parts);
			return '.' . implode('.', $parts);
			break;
	}
	return null;
}

?>