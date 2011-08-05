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
        
        echo 'teste e-mail\n\r';
        
        $params = array();
        $params["host"] = "ssl://smtp.inf.ufrgs.br";
        $params["port"] = "465";
        $params["auth"] = true;
        $params["username"] = "fanesello";
        $params["password"] = "Hookton06/10";
        $params["debug"] = true;
        
        $this->mail = Mail::factory("smtp", $params);
        
        if (PEAR::isError($this->mail)) {
            Framework::debug($this->mail->getMessage() . ", " . $this->mail->getDebugInfo());
            return FALSE;
        } else
            return TRUE;
    }
    
    public function send() {
        $recipients = "felipenesello@gmail.com";
        
        $headers = array();
        $headers["From"] = "Felipe Nesello <fanesello@inf.ufrgs.br>";
        $headers["To"] = "Felipe <felipenesello@gmail.com>";
        $headers["Reply-To"] = "fanesello@inf.ufrgs.br";
        $headers["Subject"] = "testando from php";
        
        $mailmsg = "Teste\nCorpo do e-mail";
        
        Framework::debug("antes do send");
        $ret = $this->mail->send($recipients, $headers, $mailmsg);
        Framework::debug("depois do send");
        
        if (PEAR::isError($ret)) {
            Framework::debug($ret->getMessage() . ", " . $ret->getDebugInfo());
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
}

?>
