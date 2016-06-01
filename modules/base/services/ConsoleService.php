<?php 
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\base\services;

use Yii;

/**
 * @author Maurício Quatrin Guerreiro
 */
class ConsoleService {

    /**
     * Runs console command
     *
     * @author Alexander Makarov
     * @param string $command
     * @return array [status, output]
     */
    static function run($cmd) {
        $cmd = Yii::getAlias("@app/yii") . ' ' . $cmd . ' 2>&1';
        $handler = popen($cmd, 'r');
        $output = '';
        while (!feof($handler)) {
            $output .= fgets($handler);
        }
        $output = trim($output);
        $status = pclose($handler);
        return [$status, $output];
    }
}

?>