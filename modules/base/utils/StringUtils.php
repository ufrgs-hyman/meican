<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\utils;

/**
 * @author Maurício Quatrin Guerreiro
 */
class StringUtils {
    
    public static function contains($key, $string) {
        return strpos($string, $key) !== false;
    }
}

?>