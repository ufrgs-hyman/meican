<?php
/**
 * dependencies: pear install Mail
 * pear install Net_SMTP
 */

require_once 'PEAR.php';
require_once 'Mail.php';

class Meican_Mail {
    
    private $mail;
    
    public function Meican_Mail() {
        
        $params = array();
        $params["host"] = "ssl://smtp.inf.ufrgs.br";
        $params["port"] = "465";
        $params["auth"] = true;
        $params["username"] = "meican";
        $params["password"] = "Futura2012RNP";
        $params["debug"] = false;
        
        $this->mail = Mail::factory("smtp", $params);
        
        if (PEAR::isError($this->mail)) {
            debug($this->mail->getMessage() . ", " . $this->mail->getDebugInfo());
            return FALSE;
        } else
            return TRUE;
    }
    
    public function send($to, $body, $subject=NULL, $headers=array()) {

        $tmp_headers = array();
        $tmp_headers["From"] = isset($headers["From"]) ? $headers["From"] : "MEICAN <meican@inf.ufrgs.br>";
        $tmp_headers["To"] = isset($headers["To"]) ? $headers["To"] : "$to <$to>";
        $tmp_headers["Subject"] = isset($headers["Subject"]) ? $headers["Subject"] : ($subject) ? $subject : _("No subject");

        $ret = $this->mail->send($to, $tmp_headers, $body);

        if (PEAR::isError($ret)) {
            debug($ret->getMessage() . ", " . $ret->getDebugInfo());
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
}

?>
