<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\nsi\commands;

use Yii;
use yii\console\Controller;

use meican\nsi\NSIParser;

/**
 * NSI messages and descriptions parser
 * 
 * @author Maurício Quatrin Guerreiro
 */
class ParserController extends Controller {
    
    public function actionSave($url) {
        $parser = new NSIParser();
        $parser->loadFile($url);
        $parser->parseTopology();
        #Yii::trace($parser->getData());
        $this->stdout($parser->getXml());
        return 0;
    }

    public function actionCheck($url) {
        $parser = new NSIParser();
        $parser->loadFile($url);
        $parser->parseTopology();
        #Yii::trace($parser->getData());
        $this->stdout(json_encode($parser->getData(), JSON_PRETTY_PRINT));
        return 0;
    }
}

?>
