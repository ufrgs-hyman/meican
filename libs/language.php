<?php

include_once 'libs/cookies.php';
include_once 'libs/common.php';

class Language {

    static $domain = null;

    static function getLang() {
        $lang = Common::rescueVar('lang');


        if (!$lang)
            $lang = Configure::read('defaultLang');

        return $lang;
    }

    static function setLang($domain) {

        $lang = Language::getLang();

        Language::setTranslation($lang, $domain);
    }

    static function setDomain($domain) {
        if (!is_string($domain)){
            debug($domain);
            return ;
        }
        $lang = Language::getLang();

        Language::setTranslation($lang, $domain);
    }

    static function getDomain() {
        return self::$domain;
    }

    static function refreshLangSetting($lang) {
        Common::recordVar('lang', $lang);
    }

    static function setTranslation($lang, $domain) {
        self::$domain = $domain;
        //debug(func_get_args());
        putenv("LANG=$lang");
        // e definimos tambem as informacoes de localizao padrao
        setlocale(LC_MESSAGES, $lang);
        // definimos que o domínio de textos “hello” esta localizado no diretorio “locale/”
        bindtextdomain($domain, 'i18n');
        bind_textdomain_codeset($domain, 'utf-8');
        // e definimos que iremos utilizar o dominio de textos “hello”
        textdomain($domain);
    }

}

?>
