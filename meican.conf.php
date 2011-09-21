<?php
if ($init_session)
    session_start();

@define ('__MEICAN', 1);


class DBConfig {

    function DBConfig () {

    }
}

class Framework {

    /* internal system variables */
    private static $documentRoot = null;
    private static $dirSeparator = null;
    private static $webRoot = null;
    private static $systemTimeout = 3600;
    private static $cookieLifetime = 2592000;

    /* log settings */
    private static $tmpFolder = null;
    private static $logFolder = null;
    private static $systemLogFolder = null;


    /* system profile */
    private static $systemName = 'MEICAN - Management Environment of Inter-domain Circuits for Advanced Networks';
    private static $systemNameShort = 'MEICAN';
    private static $systemVer = '';
    private static $mainApp = 'aaa';
    public static $systemDirName = 'new_meican';

    /* database settings */
    private static $defaultDatabase = 'mysql';
    private static $dbConfig = null;
    private static $defaultLang = 'pt_BR.utf8';

    public static $debugMode = 2;
    public static $useACL = false;

    /**
     * CUIDADO COM O ENDEREÇO PARA OS WEBSERVICES
     * ALTERAR DE ACORDO COM A PORTA FORNECIDA PELA NOC
     */
    public static $fedIp = 'noc.inf.ufrgs.br:65501'; // route to 143.54.12.123:80
    public static $odeIp = 'noc.inf.ufrgs.br:65401'; // route to 143.54.12.123:8080

    /**
     *
     * ODE CONFIGURACOES
     * @var $odeWSDLToRequest: reservation_info > sendForAuthorization
     * @var $odeWSDLToResponse: request_info > response
     */
    public static $odeWSDLToRequest = "http://noc.inf.ufrgs.br:65401/ode/deployment/bundles/v4_felipe_workflow/processes/v4_felipe_workflow/processes.ode/diagrama-ODE_Workflow_Felipe.wsdl";
    public static $odeWSDLToResponse = "http://noc.inf.ufrgs.br:65401/ode/deployment/bundles/v4_felipe_workflow/processes/v4_felipe_workflow/processes.ode/diagrama-ODE_Workflow_Felipe.wsdl";


    /**
     * NÃO ALTERAR
     */
    public static $OSCARSBridgeEPR = 'http://143.54.12.123:8080/axis2/services/OSCARSBridge?wsdl';


    static function init () {
        if (Framework::$documentRoot == null) {

            Framework::$documentRoot = dirname(__FILE__);

            // Root setting
            if ( Framework::isLinuxOperatingSystem() || Framework::isSunOperatingSystem() || Framework::isMacOperatingSystem ()) {
                Framework::$tmpFolder = Framework::$documentRoot.'/tmp/';
                Framework::$logFolder = Framework::$documentRoot.'/logs/';
                //Framework::$debugLogFolder = Framework::$documentRoot.'/logs/';

                //Framework::$networkDiscoveryLogFolder = Framework::$documentRoot.'/logs/networkDiscovery/';
                //Framework::$ticketsLogFolder = Framework::$documentRoot.'/logs/ticketsManagement/';
                //Framework::$trapsManagementLogFolder = Framework::$documentRoot.'/logs/trapsManagement/';
                //Framework::$systemLogFolder = Framework::$documentRoot.'/logs/systemManagement/';

                Framework::$dirSeparator = "/";

            } else if (Framework::isWindowsOperatingSystem()) {
                Framework::$tmpFolder = Framework::$documentRoot.'\\tmp\\';
                Framework::$logFolder = Framework::$documentRoot.'\\logs\\';
                Framework::$debugLogFolder = Framework::$documentRoot.'\\logs\\';

                Framework::$networkDiscoveryLogFolder = Framework::$documentRoot.'\\logs\\networkDiscovery\\';
                Framework::$ticketsLogFolder = Framework::$documentRoot.'\\logs\\ticketsManagement\\';
                Framework::$trapsManagementLogFolder = Framework::$documentRoot.'\\logs\\trapsManagement\\';
                Framework::$systemLogFolder = Framework::$documentRoot.'\\logs\\systemManagement\\';

                Framework::$dirSeparator = "\\";

            } else {
                die ("Operating System not supported: " . Framework::getOperatingSystemVersion ());
            }

            Framework::$systemTimeout = 3600;

            // db settings
            if (Framework::$dbConfig == null) {
                Framework::$dbConfig = array ();

                /**
                 * aqui vão as confs do BD
                 */
                Framework::$dbConfig ['mysql'] = new DBConfig ();
                Framework::$dbConfig ['mysql']->db_driver = 'mysql';
                Framework::$dbConfig ['mysql']->db_user = 'root';
                Framework::$dbConfig ['mysql']->db_pass = 'futurarnp';
                Framework::$dbConfig ['mysql']->db_host = 'localhost';
                Framework::$dbConfig ['mysql']->db_name = 'meican';

                Framework::$dbConfig ['oracle'] = new DBConfig ();
                Framework::$dbConfig ['oracle']->db_driver = 'oci8';
                Framework::$dbConfig ['oracle']->db_user = 'digistar';
                Framework::$dbConfig ['oracle']->db_pass = 'digistar';
                Framework::$dbConfig ['oracle']->db_host = '//localhost';
                Framework::$dbConfig ['oracle']->db_name = 'XE';

            }
        }

        chDir (Framework::$documentRoot);
    }

    static function initWebRoot () {
        if (Framework::$webRoot == null) {
            Framework::$webRoot = dirname ($_SERVER ['PHP_SELF']);
            $_SESSION ['webRoot'] = Framework::$webRoot;
        }
    }

    static function getWebRoot () {
        if (Framework::$webRoot == null) {
            Framework::$webRoot = $_SESSION ['webRoot'];
        }
        return Framework::$webRoot;
    }

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

    static function getSystemPath () {
        return Framework::$documentRoot;
    }

    static function getLogPath () {
        return Framework::$logFolder;
    }

    static function getTmpPath () {
        return Framework::$tmpFolder;
    }

    static function getDebugLogPath () {
        return Framework::$debugLogFolder;
    }

    static function getSystemLogPath () {
        return Framework::$systemLogFolder;
    }

    static function getTrapsManagementLogPath () {
        return Framework::$trapsManagementLogFolder;
    }

    static function getNetworkDiscoveryLogPath () {
        return Framework::$networkDiscoveryLogFolder;
    }

    static function getTicketsLogPath () {
        return Framework::$ticketsLogFolder;
    }

    static function getDirSeparator () {
        return Framework::$dirSeparator;
    }

    static function getSystemName () {
        return Framework::$systemName;
    }

    static function getSystemNameShort () {
        return Framework::$systemNameShort;
    }

    static function getSystemTimeout () {
        return Framework::$systemTimeout;
    }

    static function getCookieLifetime () {
        return Framework::$cookieLifetime;
    }

    static function getMainApp () {
        return Framework::$mainApp;
    }

    static function getDefaultDatabase () {
        return Framework::$defaultDatabase;
    }

    static function getDefaultLang() {
        return Framework::$defaultLang;
    }

    static function getDatabaseString () {
        $obj = Framework::$dbConfig [Framework::$defaultDatabase];

        return $obj->db_driver . '://'. $obj->db_user . ':' . $obj->db_pass . '@' . $obj->db_host . '/' . $obj->db_name;
    }

    static function getDatabaseSettings ($db) {
        return Framework::$dbConfig [$db];
    }

    static function loadApp($app) {
        if (file_exists("apps/$app/$app.php")) {
            include_once "apps/$app/$app.php";

            if (class_exists($app)) {
                return new $app;
            }
        }
        return FALSE;
    }

    static function debug($msg, $var) {

        if (Framework::$debugMode) {
            $fileName = '/var/www/'.Framework::$systemDirName.'/log/log.txt';

            if (isset($var)) {
                ob_start();
                print_r($var);
                $result = ob_get_clean();
                file_put_contents($fileName, date("d/m/Y G:i:s").": $msg: $result\r\n", FILE_APPEND);
            } else {
                file_put_contents($fileName, date("d/m/Y G:i:s").": $msg\r\n", FILE_APPEND);
            }
        }
    }

}

Framework::init();

?>
