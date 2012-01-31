<?php

@define ('__MEICAN', 1);

class Framework {

    static function getOperatingSystemVersion () {
        return strtoupper(substr(PHP_OS, 0, 3));
    }

    static function isLinuxOperatingSystem () {
        return (strtoupper(substr(PHP_OS, 0, 3)) == 'LIN');
    }

    static function isSunOperatingSystem () {
        return (strtoupper(substr(PHP_OS, 0, 3)) == 'SUN');
    }

    static function isWindowsOperatingSystem () {
        return (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
    }

    static function isMacOperatingSystem () {
        return (strtoupper(substr(PHP_OS, 0, 3)) == 'DAR');
    }

    static function debug($msg, $var=NULL) {

        if (Configure::read('debug')) {
            $fileName = ROOT.DS.'log/log.txt';
            
            if ($var !== NULL) {
                file_put_contents($fileName, date("d/m/Y G:i:s").": $msg: ".print_r($var, true)."\r\n", FILE_APPEND);
            } else {
                file_put_contents($fileName, date("d/m/Y G:i:s").": $msg\r\n", FILE_APPEND);
            }
        }
    }

}
