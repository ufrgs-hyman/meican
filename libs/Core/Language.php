<?php

include_once 'libs/common.php';

class Language {
    
    
    private $language = null;
    private $domain = null;
    
    public function __construct(){
        $language = Common::getSessionVariable('lang');
        if (!$language)
            $language = Configure::read('defaultLang');
        $this->setLanguage($language);
    }
    
    public static function getInstance(){
        static $instance = null;

        if (!$instance) {
            $c = __CLASS__;
            $instance = new $c;
        }
        return $instance;
    }
    
    public function setDomain($domain){
        if ($domain != $this->domain){
            $this->domain = $domain;
            
            bindtextdomain($domain, 'i18n');
            bind_textdomain_codeset($domain, 'utf-8');
            // e definimos que iremos utilizar o dominio de textos “hello”
            textdomain($domain);
        }
        
    }
    
    public function setLanguage($language){
        if ($language != $this->language){
            $this->language = $language;
            Common::setSessionVariable('lang', $language);
            
            putenv("LANG=$language");
            // e definimos tambem as informacoes de localizao padrao
            setlocale(LC_MESSAGES, $language);
        }
    }
    
    public function getDomain(){
        return $this->domain;
    }
    
    public function getLanguage(){
        return $this->language;
    }
    
    public function loadLanguageFromHeader(){
        //TODO: implement
    }

}
