<?php

include_once 'libs/cookies.php';
include_once 'libs/Model/database.php';

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
 * @param boolean $showHtml If set to true, the method prints the debug data in a browser-friendly way.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @link http://book.cakephp.org/2.0/en/development/debugging.html#basic-debugging
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#debug
 */
function debug($var = false, $showHtml = null, $showFrom = true) {
	if (Configure::read('debug') > 0) {
		$file = '';
		$line = '';
		$lineInfo = '';
		if ($showFrom) {
			$calledFrom = debug_backtrace();
			$file = substr(str_ireplace(ROOT, '', $calledFrom[0]['file']), 1);
			$line = $calledFrom[0]['line'];
		}
		$html = <<<HTML
<div class="cake-debug-output">
%s
<pre class="cake-debug">
%s
</pre>
</div>
HTML;
		$text = <<<TEXT
%s
########## DEBUG ##########
%s
###########################
TEXT;
		$template = $html;
		if (php_sapi_name() == 'cli' || $showHtml === false) {
			$template = $text;
			if ($showFrom) {
				$lineInfo = sprintf('%s (line %s)', $file, $line);
			}
		}
		if ($showHtml === null && $template !== $text) {
			$showHtml = true;
		}
		$var = print_r($var, true);
		if ($showHtml) {
			$template = $html;
			$var = h($var);
			if ($showFrom) {
				$lineInfo = sprintf('<span><strong>%s</strong> (line <strong>%s</strong>)</span>', $file, $line);
			}
		}
		printf($template, $lineInfo, $var);
	}
    Log::write('debug', str_replace('<', '&lt;', str_replace('>', '&gt;', $var)));
}



/**
 * Convenience method for htmlspecialchars.
 *
 * @param mixed $text Text to wrap through htmlspecialchars.  Also works with arrays, and objects.
 *    Arrays will be mapped and have all their elements escaped.  Objects will be string cast if they
 *    implement a `__toString` method.  Otherwise the class name will be used.
 * @param boolean $double Encode existing html entities
 * @param string $charset Character set to use when escaping.  Defaults to config value in 'App.encoding' or 'UTF-8'
 * @return string Wrapped text
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#h
 */
function h($text, $double = true, $charset = null) {
	if (is_array($text)) {
		$texts = array();
		foreach ($text as $k => $t) {
			$texts[$k] = h($t, $double, $charset);
		}
		return $texts;
	} elseif (is_object($text)) {
		if (method_exists($text, '__toString')) {
			$text = (string) $text;
		} else {
			$text = '(object)' . get_class($text);
		}
	}

	static $defaultCharset = false;
	if ($defaultCharset === false) {
		$defaultCharset = Configure::read('App.encoding');
		if ($defaultCharset === null) {
			$defaultCharset = 'UTF-8';
		}
	}
	if (is_string($double)) {
		$charset = $double;
	}
	return htmlspecialchars($text, ENT_QUOTES, ($charset) ? $charset : $defaultCharset, $double);
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

if (!function_exists('getMicrotime')) {

/**
 * Returns microtime for execution time checking
 *
 * @return float Microtime
 */
	function getMicrotime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}

/**
 * Splits a dot syntax plugin name into its plugin and classname.
 * If $name does not have a dot, then index 0 will be null.
 *
 * Commonly used like `list($plugin, $name) = pluginSplit($name);`
 *
 * @param string $name The name you want to plugin split.
 * @param boolean $dotAppend Set to true if you want the plugin to have a '.' appended to it.
 * @param string $plugin Optional default plugin to use if no plugin is found. Defaults to null.
 * @return array Array with 2 indexes.  0 => plugin name, 1 => classname
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#pluginSplit
 */
function pluginSplit($name, $dotAppend = false, $plugin = null) {
	if (strpos($name, '.') !== false) {
		$parts = explode('.', $name, 2);
		if ($dotAppend) {
			$parts[0] .= '.';
		}
		return $parts;
	}
	return array($plugin, $name);
}

?>