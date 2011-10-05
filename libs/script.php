<?php
include_once 'libs/common.php';

class Script {

    public $jsFiles = array();
    public $scriptArgs = array();
    public $content = NULL;

    function Script() {

    }

    public function setArgs($args) {
        if ($args) {

            $loadedVars = Common::getSessionVariable('script_vars');

            if (false && $loadedVars) {

                foreach ($args as $ind => $val) {
                    if (isset($loadedVars[$ind])) {

                        if ($loadedVars[$ind] == $val) { //existe e igual, nada a fazer
                            continue;

                        } else { //existe e é diferente, refresh na página para atualizar variaveis
                            $loadedVars[$ind] = $val;
                            $this->scriptArgs[$ind] = $val;
                            //header('HTTP/1.1 406 Force Refresh');
                        }
                    } else { //nao existe a variavel, vai apenas acrescentar, nao oferece risco de conflito de variavel no javascript
                        $this->scriptArgs[$ind] = $val;
                        $loadedVars[$ind] = $val;
                    }
                    Common::setSessionVariable('script_vars', $loadedVars);
                    //Framework::debug("loading vars", $val);
                }

            } else { //nao existe nada carregado de variaveis
                $this->scriptArgs = $args;
                $loadedVars = $args;
                Common::setSessionVariable('script_vars', $loadedVars);
                //Framework::debug("loading vars first time", $args);
            }

        }
    }

    public function setScriptFiles($scriptFiles) {
        if ($scriptFiles) {
            $loadedScripts = Common::getSessionVariable('scripts');

            if ($loadedScripts) {
                foreach ($scriptFiles as $js) {
                    if (array_search($js, $loadedScripts) === FALSE) { //vai incluir
                        $this->jsFiles[] = $js;
                        array_push($loadedScripts, $js);
                        Common::setSessionVariable('scripts', $loadedScripts);
                        //Framework::debug("loading script", $js);
                    }
                }
            } else {
                $this->jsFiles = $scriptFiles;
                Common::setSessionVariable('scripts', $scriptFiles);
                //Framework::debug("loading scripts first time", $scriptFiles);
            }
        }
    }

    public function setInlineScript($scriptFile) {
        if ($scriptFile)
            $this->jsFiles[] = $scriptFile;
    }

    public function build() {
        ob_start();
        include('layouts/script.php');
        $this->content = ob_get_contents();
        ob_end_clean();
    }

//    public function buildHeader($argsToHeader=null) {
//        $this->setHeader();
//        $this->passedArgs = $argsToHeader;
//        $args = array(0 => "val1", 1 => "val2", 2 => "val3");
//        $scriptVars = array("str" => "essa uma string", "num" => 4, "json" => $args);
//
//        if (count($this->header) > 0) {
//            ob_start();
//            foreach ($this->header as $h)
//                include($h);
//            $this->headerContent = ob_get_contents();
//            ob_end_clean();
//        } else
//            $this->headerContent = NULL;
//
//        if ($this->headerContent)
//            return $this->headerContent;
//        else
//            return FALSE;
//    }
//
//    private function setHeader() {
//        $tempHeader = "apps/$this->app/views/headers/$this->controller.php";
//
//        if (file_exists($tempHeader)) {
//            $this->header[] = $tempHeader;
//        }
//        if ($this->action) {
//            $tempHeader = "apps/$this->app/views/headers/$this->controller".'_'."$this->action.php";
//            if (file_exists($tempHeader))
//                $this->header[] = $tempHeader;
//        }
//    }

}

?>
