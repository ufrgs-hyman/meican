<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\utils;

/**
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
class ColorUtils {

    static function generate() {
        return sprintf('#%02X%02X%02X', rand(0, 255), rand(0, 255), rand(0, 255)); 
    }

}