<?php
include_once 'meican.conf.php';
include_once 'libs/cookies.php';
include_once 'libs/common.php';

class Language {

     static function getLang(){
         $lang = Common::rescueVar('lang');
      

        if (!$lang)
             $lang = Framework::getDefaultLang();

        return $lang;

    }


    static function setLang($domain) {

        $lang = Language::getLang();

        Language::setTranslation($lang, $domain);

    }



    static function refreshLangSetting($lang) {
            Common::recordVar('lang', $lang);
    }

    static function setTranslation($lang, $domain){

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
