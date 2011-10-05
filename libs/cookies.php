<?php

class CookiesSystem {


/**
 * This function returns the value for the cookie passed as parameter,
 * or 'default' in case the cookie is not found
 *
 * @param $cookie
 * @param $default
 * @return unknown_type
 */
    static function getCookie($cookie) {
        if (isset($_COOKIE[$cookie])) {
            return $_COOKIE[$cookie];
        }
        return FALSE;
    }

    /**
     * This function saves the cookie passed as parameter,
     * using the informed expiration time
     *
     * @param $cookie_name
     * @param $cookie_value
     * @param $expire
     * @return unknown_type
     */
    static function setCookie ($cookie_name, $cookie_value, $expire = 0) {
        if ($expire == 0) {
            $expire = time() + Framework::getCookieLifetime();
        }
        setcookie($cookie_name, $cookie_value, $expire);
    }

}

?>
